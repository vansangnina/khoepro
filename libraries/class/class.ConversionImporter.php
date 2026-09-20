<?php
/**
 * FITNADO Affiliate Conversion & CSV Importer
 * Phase 08: Analytics, Affiliate Attribution & Winner Detection
 * PHP 7.4 Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

class ConversionImporter {
    private $d;

    // Platform Constants
    const PLATFORMS = array(
        'shopee' => 'Shopee Affiliate',
        'tiktok_shop' => 'TikTok Shop Affiliate',
        'lazada' => 'Lazada Affiliate',
        'tiki' => 'Tiki Affiliate',
        'brand' => 'Brand Direct',
        'manual' => 'Manual Entry'
    );

    // Status Constants
    const STATUS_PENDING   = 'PENDING';
    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_REVERSED  = 'REVERSED';
    const STATUS_CANCELLED = 'CANCELLED';

    public function __construct($d = null) {
        $this->d = $d;
    }

    /**
     * Parse CSV File into Associative Array of Rows
     * Supports UTF-8 BOM, comma, and semicolon delimiters
     * @param string $filePath
     * @return array ['success' => bool, 'headers' => array, 'rows' => array, 'error' => string|null]
     */
    public function parseCsvFile($filePath) {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return array('success' => false, 'error' => 'File CSV không tồn tại hoặc không thể đọc.');
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return array('success' => false, 'error' => 'Không thể mở file CSV.');
        }

        // Read first line to detect delimiter and handle BOM
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return array('success' => false, 'error' => 'File CSV rỗng.');
        }

        // Strip UTF-8 BOM if present
        $bom = pack('H*', 'EFBBBF');
        $firstLine = preg_replace("/^{$bom}/", '', $firstLine);

        // Detect delimiter
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
        
        rewind($handle);
        // Skip BOM on stream if present
        $headerCandidate = fgetcsv($handle, 4096, $delimiter);
        if (empty($headerCandidate)) {
            fclose($handle);
            return array('success' => false, 'error' => 'Không thể đọc tiêu đề cột CSV.');
        }

        $headers = array();
        foreach ($headerCandidate as $h) {
            $cleaned = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h));
            $headers[] = strtolower($cleaned ?: trim($h));
        }

        $rows = array();
        $lineNum = 1;
        while (($data = fgetcsv($handle, 4096, $delimiter)) !== false) {
            $lineNum++;
            if (empty($data) || (count($data) === 1 && $data[0] === null)) {
                continue; // Skip blank line
            }

            $row = array();
            foreach ($headers as $idx => $headerName) {
                $row[$headerName] = isset($data[$idx]) ? trim($data[$idx]) : '';
            }
            $row['_line_number'] = $lineNum;
            $rows[] = $row;
        }

        fclose($handle);

        return array(
            'success' => true,
            'headers' => $headers,
            'rows' => $rows,
            'total_rows' => count($rows)
        );
    }

    /**
     * Map Raw CSV Row into Normalized Conversion Record
     * @param array $rawRow
     * @param string $defaultPlatform
     * @return array
     */
    public function mapRow(array $rawRow, $defaultPlatform = 'shopee') {
        // Platform Resolution
        $platform = $defaultPlatform;
        foreach (array('platform', 'san', 'network', 'source_platform') as $k) {
            if (!empty($rawRow[$k])) {
                $platform = strtolower(trim($rawRow[$k]));
                break;
            }
        }
        if (strpos($platform, 'tiktok') !== false) $platform = 'tiktok_shop';
        elseif (strpos($platform, 'shopee') !== false) $platform = 'shopee';
        elseif (strpos($platform, 'lazada') !== false) $platform = 'lazada';
        elseif (strpos($platform, 'tiki') !== false) $platform = 'tiki';
        elseif (!isset(self::PLATFORMS[$platform])) $platform = 'shopee';

        // External Conversion ID (Order ID / SubID / ClickID)
        $externalId = '';
        foreach (array('external_conversion_id', 'order_id', 'conversion_id', 'ma_don_hang', 'sub_id', 'click_id', 'id') as $k) {
            if (!empty($rawRow[$k])) {
                $externalId = trim($rawRow[$k]);
                break;
            }
        }

        // Tracking Code / Sub_ID1
        $trackingCode = '';
        foreach (array('tracking_code', 'sub_id_1', 'sub1', 'utm_content', 'ref', 'aff_sub') as $k) {
            if (!empty($rawRow[$k])) {
                $trackingCode = trim($rawRow[$k]);
                break;
            }
        }

        // Order Value
        $orderValue = 0.0;
        foreach (array('order_value', 'gia_tri_don', 'gmv', 'amount', 'total_amount') as $k) {
            if (isset($rawRow[$k]) && $rawRow[$k] !== '') {
                $clean = str_replace(array(',', ' ', 'VND', 'vnd', '$'), '', $rawRow[$k]);
                $orderValue = (float)$clean;
                break;
            }
        }

        // Commission Value
        $commissionValue = 0.0;
        foreach (array('commission_value', 'commission', 'hoa_hong', 'payout', 'comm') as $k) {
            if (isset($rawRow[$k]) && $rawRow[$k] !== '') {
                $clean = str_replace(array(',', ' ', 'VND', 'vnd', '$'), '', $rawRow[$k]);
                $commissionValue = (float)$clean;
                break;
            }
        }

        // Currency
        $currency = 'VND';
        foreach (array('currency', 'tien_te', 'curr') as $k) {
            if (!empty($rawRow[$k])) {
                $currency = strtoupper(trim($rawRow[$k]));
                break;
            }
        }

        // Status
        $status = self::STATUS_CONFIRMED;
        foreach (array('status', 'trang_thai', 'order_status') as $k) {
            if (!empty($rawRow[$k])) {
                $st = strtoupper(trim($rawRow[$k]));
                if (in_array($st, array('PENDING', 'CHO_DUYET', 'WAITING'))) $status = self::STATUS_PENDING;
                elseif (in_array($st, array('CONFIRMED', 'THANH_CONG', 'APPROVED', 'PAID', 'HOAN_THANH'))) $status = self::STATUS_CONFIRMED;
                elseif (in_array($st, array('REVERSED', 'HOAN_TIEN', 'REFUNDED', 'RETURNED'))) $status = self::STATUS_REVERSED;
                elseif (in_array($st, array('CANCELLED', 'DA_HUY', 'REJECTED'))) $status = self::STATUS_CANCELLED;
                break;
            }
        }

        // Conversion & Confirmation Timestamp
        $conversionAt = time();
        foreach (array('conversion_at', 'thoi_gian_dat', 'created_at', 'order_time', 'date') as $k) {
            if (!empty($rawRow[$k])) {
                $ts = strtotime($rawRow[$k]);
                if ($ts !== false && $ts > 0) {
                    $conversionAt = $ts;
                    break;
                }
            }
        }

        return array(
            'platform' => $platform,
            'external_conversion_id' => $externalId,
            'tracking_code' => $trackingCode,
            'order_value' => $orderValue,
            'commission_value' => $commissionValue,
            'currency' => $currency,
            'status' => $status,
            'conversion_at' => $conversionAt,
            'raw_row' => $rawRow
        );
    }

    /**
     * Preview CSV Import: Validates rows, checks duplicates and matching attribution
     * @param string $filePath
     * @param string $defaultPlatform
     * @return array
     */
    public function previewCsv($filePath, $defaultPlatform = 'shopee') {
        $parsed = $this->parseCsvFile($filePath);
        if (!$parsed['success']) {
            return $parsed;
        }

        $validRows = array();
        $invalidRows = array();
        $duplicateRows = array();
        $unattributedCount = 0;
        $matchedCount = 0;

        $totalOrderValVND = 0.0;
        $totalCommissionVND = 0.0;

        foreach ($parsed['rows'] as $rawRow) {
            $mapped = $this->mapRow($rawRow, $defaultPlatform);
            $lineNum = $rawRow['_line_number'] ?? 0;

            // Validation: Must have external_conversion_id
            if (empty($mapped['external_conversion_id'])) {
                $invalidRows[] = array(
                    'line' => $lineNum,
                    'error' => 'Thiếu mã đơn hàng (external_conversion_id / order_id).',
                    'data' => $mapped
                );
                continue;
            }

            // Check Duplicate in DB (Idempotency)
            $exists = null;
            if ($this->d) {
                $exists = $this->d->rawQueryOne(
                    "SELECT id, status, commission_value FROM table_affiliate_conversion WHERE platform = ? AND external_conversion_id = ? LIMIT 1",
                    array($mapped['platform'], $mapped['external_conversion_id'])
                );
            }

            if (!empty($exists)) {
                $duplicateRows[] = array(
                    'line' => $lineNum,
                    'existing_id' => $exists['id'],
                    'existing_status' => $exists['status'],
                    'new_status' => $mapped['status'],
                    'data' => $mapped
                );
                continue;
            }

            // Check Attribution Matching
            $matchedContext = $this->resolveAttributionForConversion($mapped['tracking_code']);
            $mapped['is_attributed'] = !empty($matchedContext['id_product']);
            $mapped['matched_context'] = $matchedContext;

            if ($mapped['is_attributed']) {
                $matchedCount++;
            } else {
                $unattributedCount++;
            }

            if ($mapped['currency'] === 'VND') {
                $totalOrderValVND += $mapped['order_value'];
                $totalCommissionVND += $mapped['commission_value'];
            }

            $validRows[] = $mapped;
        }

        return array(
            'success' => true,
            'total_rows' => count($parsed['rows']),
            'valid_count' => count($validRows),
            'invalid_count' => count($invalidRows),
            'duplicate_count' => count($duplicateRows),
            'matched_count' => $matchedCount,
            'unattributed_count' => $unattributedCount,
            'total_order_value_vnd' => $totalOrderValVND,
            'total_commission_vnd' => $totalCommissionVND,
            'valid_rows' => $validRows,
            'invalid_rows' => $invalidRows,
            'duplicate_rows' => $duplicateRows
        );
    }

    /**
     * Resolve Attribution Context from Tracking Code
     * @param string $trackingCode
     * @return array [id_product, id_post, id_video, id_content, id_affiliate_offer]
     */
    public function resolveAttributionForConversion($trackingCode = '') {
        $result = array(
            'id_product' => null,
            'id_post' => null,
            'id_video' => null,
            'id_content' => null,
            'id_affiliate_offer' => null
        );

        if (empty($trackingCode) || !$this->d) {
            return $result;
        }

        // 1. Try matching table_publish_post by tracking_code
        $post = $this->d->rawQueryOne("SELECT * FROM table_publish_post WHERE tracking_code = ? LIMIT 1", array($trackingCode));
        if (!empty($post)) {
            $result['id_post'] = (int)$post['id'];
            $result['id_product'] = (int)$post['id_product'];
            $result['id_video'] = !empty($post['id_video']) ? (int)$post['id_video'] : null;
            $result['id_content'] = !empty($post['id_ai_content']) ? (int)$post['id_ai_content'] : null;
            $result['id_affiliate_offer'] = !empty($post['affiliate_offer_id']) ? (int)$post['affiliate_offer_id'] : null;
            return $result;
        }

        // 2. Try matching table_affiliate_click by tracking_code
        $click = $this->d->rawQueryOne("SELECT * FROM table_affiliate_click WHERE tracking_code = ? ORDER BY id DESC LIMIT 1", array($trackingCode));
        if (!empty($click)) {
            $result['id_product'] = !empty($click['id_product']) ? (int)$click['id_product'] : null;
            $result['id_post'] = !empty($click['id_post']) ? (int)$click['id_post'] : null;
            $result['id_video'] = !empty($click['id_video']) ? (int)$click['id_video'] : null;
            $result['id_content'] = !empty($click['id_content']) ? (int)$click['id_content'] : null;
            $result['id_affiliate_offer'] = !empty($click['id_affiliate']) ? (int)$click['id_affiliate'] : null;
            return $result;
        }

        return $result;
    }

    /**
     * Execute Import Transaction
     * Safely inserts valid conversions and logs audit history
     * @param string $filePath
     * @param string $platform
     * @param string $adminUser
     * @return array
     */
    public function executeImport($filePath, $platform = 'shopee', $adminUser = 'admin') {
        $preview = $this->previewCsv($filePath, $platform);
        if (!$preview['success']) {
            return $preview;
        }

        $now = time();
        $filename = basename($filePath);

        // Create Import Log Entry
        $importLogId = $this->d->insert('conversion_import_log', array(
            'filename' => $filename,
            'platform' => $platform,
            'total_rows' => $preview['total_rows'],
            'imported_count' => 0,
            'duplicate_count' => $preview['duplicate_count'],
            'failed_count' => $preview['invalid_count'],
            'admin_user' => $adminUser,
            'status' => 'PROCESSING',
            'summary_json' => json_encode(array(
                'valid_count' => $preview['valid_count'],
                'matched_count' => $preview['matched_count'],
                'unattributed_count' => $preview['unattributed_count'],
                'commission_vnd' => $preview['total_commission_vnd']
            )),
            'date_created' => $now
        ));

        $importedCount = 0;
        $failedCount = $preview['invalid_count'];

        foreach ($preview['valid_rows'] as $row) {
            $ctx = $row['matched_context'];

            $conversionData = array(
                'platform' => $row['platform'],
                'external_conversion_id' => $row['external_conversion_id'],
                'id_product' => $ctx['id_product'],
                'id_affiliate_offer' => $ctx['id_affiliate_offer'],
                'id_post' => $ctx['id_post'],
                'id_video' => $ctx['id_video'],
                'id_content' => $ctx['id_content'],
                'tracking_code' => $row['tracking_code'],
                'session_id' => null,
                'order_value' => (float)$row['order_value'],
                'commission_value' => (float)$row['commission_value'],
                'currency' => $row['currency'],
                'status' => $row['status'],
                'conversion_at' => (int)$row['conversion_at'],
                'confirmed_at' => ($row['status'] === self::STATUS_CONFIRMED) ? $now : null,
                'raw_reference' => json_encode($row['raw_row']),
                'import_id' => $importLogId,
                'is_manual_matched' => 0,
                'date_created' => $now,
                'date_updated' => $now
            );

            try {
                $insId = $this->d->insert('affiliate_conversion', $conversionData);
                if ($insId) {
                    $importedCount++;

                    // Also project CONVERSION event into table_analytics_event
                    $analytics = new AnalyticsService($this->d);
                    $analytics->logEvent(AnalyticsService::EVENT_CONVERSION, array(
                        'id_product' => $ctx['id_product'],
                        'id_post' => $ctx['id_post'],
                        'id_video' => $ctx['id_video'],
                        'id_content' => $ctx['id_content'],
                        'id_affiliate_offer' => $ctx['id_affiliate_offer'],
                        'tracking_code' => $row['tracking_code'],
                        'source' => $row['platform'],
                        'medium' => 'affiliate_conversion',
                        'metadata' => array(
                            'conversion_id' => $insId,
                            'external_id' => $row['external_conversion_id'],
                            'order_value' => $row['order_value'],
                            'commission_value' => $row['commission_value'],
                            'currency' => $row['currency'],
                            'status' => $row['status']
                        ),
                        'event_time' => (int)$row['conversion_at']
                    ));
                } else {
                    $failedCount++;
                }
            } catch (Exception $e) {
                $failedCount++;
            }
        }

        // Handle duplicates that need status updates (e.g. PENDING -> REVERSED)
        foreach ($preview['duplicate_rows'] as $dup) {
            if ($dup['existing_status'] !== $dup['new_status']) {
                $this->d->where('id', $dup['existing_id']);
                $this->d->update('affiliate_conversion', array(
                    'status' => $dup['new_status'],
                    'date_updated' => $now
                ));
            }
        }

        // Update Final Import Log Status
        $finalStatus = ($failedCount === 0) ? 'SUCCESS' : ($importedCount > 0 ? 'PARTIAL' : 'FAILED');
        $this->d->where('id', $importLogId);
        $this->d->update('conversion_import_log', array(
            'imported_count' => $importedCount,
            'failed_count' => $failedCount,
            'status' => $finalStatus
        ));

        return array(
            'success' => true,
            'import_log_id' => $importLogId,
            'total_rows' => $preview['total_rows'],
            'imported_count' => $importedCount,
            'duplicate_count' => $preview['duplicate_count'],
            'failed_count' => $failedCount,
            'matched_count' => $preview['matched_count'],
            'unattributed_count' => $preview['unattributed_count'],
            'status' => $finalStatus
        );
    }

    /**
     * Manually Match Unattributed Conversion to a Product / Post
     * Audit log with admin name and notes
     * @param int $conversionId
     * @param int $productId
     * @param int|null $postId
     * @param string $adminUser
     * @param string $notes
     * @return bool
     */
    public function manualMatchConversion($conversionId, $productId, $postId = null, $adminUser = 'admin', $notes = '') {
        if (!$this->d) return false;

        $conversionId = (int)$conversionId;
        $productId = (int)$productId;
        $postId = $postId ? (int)$postId : null;

        $conv = $this->d->rawQueryOne("SELECT * FROM table_affiliate_conversion WHERE id = ? LIMIT 1", array($conversionId));
        if (empty($conv)) return false;

        $idVideo = null;
        $idContent = null;
        if ($postId) {
            $post = $this->d->rawQueryOne("SELECT * FROM table_publish_post WHERE id = ? LIMIT 1", array($postId));
            if (!empty($post)) {
                $idVideo = !empty($post['id_video']) ? (int)$post['id_video'] : null;
                $idContent = !empty($post['id_ai_content']) ? (int)$post['id_ai_content'] : null;
            }
        }

        $this->d->where('id', $conversionId);
        $res = $this->d->update('affiliate_conversion', array(
            'id_product' => $productId,
            'id_post' => $postId,
            'id_video' => $idVideo,
            'id_content' => $idContent,
            'is_manual_matched' => 1,
            'matched_by' => $adminUser,
            'match_notes' => trim($notes),
            'date_updated' => time()
        ));

        return (bool)$res;
    }
}
