<?php
/**
 * KHOEPRO - Content Candidate Engine
 * Automated high-potential product selection & multi-platform scoring for Content Automation.
 * PHP 7.4 & 8.x Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

if (!class_exists('ProductResearch')) {
    require_once __DIR__ . '/class.ProductResearch.php';
}

class ContentCandidateEngine
{
    private $d;
    private $func;
    private $researchEngine;

    const STATUS_PENDING    = 'PENDING';
    const STATUS_APPROVED   = 'APPROVED';
    const STATUS_PROCESSED  = 'PROCESSED';
    const STATUS_DISCARDED  = 'DISCARDED';

    public function __construct($d = null, $func = null, $researchEngine = null)
    {
        $this->d = $d;
        $this->func = $func;
        $this->researchEngine = $researchEngine ?: new ProductResearch($d, $func);
    }

    /**
     * Scan catalog and research products, evaluate eligibility, calculate platform scores,
     * and populate table_content_candidate idempotently with human-readable reasons.
     * 
     * @param array $options ['limit' => 50, 'min_score' => 60.0, 'platform' => 'all']
     * @return array ['success' => bool, 'scanned' => int, 'candidates_created' => int, 'candidates_updated' => int, 'skipped' => int]
     */
    public function scanAndRankCandidates(array $options = array())
    {
        if (!$this->d) {
            return array('success' => false, 'error' => 'Database connection unavailable.');
        }

        $limit = min(100, max(1, (int)($options['limit'] ?? 50)));
        $minScore = (float)($options['min_score'] ?? 50.0);
        $targetPlatform = $options['platform'] ?? 'all';
        $now = time();

        // 1. Fetch live active products with affiliate offers
        $sql = "SELECT p.*, a.affiliate_url, a.platform as affiliate_platform, a.commission_rate, a.commission_value,
                       r.id as research_id, r.demand_score, r.content_score, r.competition_score, r.seo_score,
                       r.problem_solved, r.target_audience, r.primary_keyword, r.top_video_views, r.sales_count, r.rating
                FROM table_product p
                INNER JOIN table_product_affiliate a ON p.id = a.id_product AND FIND_IN_SET('hienthi', a.status)
                LEFT JOIN table_product_research r ON (p.id = r.id_product OR p.code = r.external_product_id)
                WHERE FIND_IN_SET('hienthi', p.status)
                ORDER BY p.id DESC LIMIT " . $limit;

        $products = $this->d->rawQuery($sql);
        if (empty($products)) {
            return array('success' => true, 'scanned' => 0, 'candidates_created' => 0, 'candidates_updated' => 0, 'skipped' => 0);
        }

        $createdCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($products as $prod) {
            $productId = (int)$prod['id'];

            // Eligibility Gate
            $eligibility = $this->researchEngine->checkEligibilityForContent($prod, $targetPlatform);
            if (!$eligibility['eligible']) {
                $skippedCount++;
                continue;
            }

            // Platform Scoring
            $platformScores = $this->researchEngine->calculatePlatformScores($prod);
            $globalScore = (float)$platformScores['global_score'];
            $tiktokScore = (float)$platformScores['tiktok_score'];
            $fbScore = (float)$platformScores['facebook_score'];
            $ytScore = (float)$platformScores['youtube_score'];

            if ($globalScore < $minScore && $tiktokScore < $minScore && $fbScore < $minScore) {
                $skippedCount++;
                continue;
            }

            // Build detailed selection reason
            $reasons = array();
            $reasons[] = "Điểm tổng quan: " . number_format($globalScore, 1) . "/100.";
            if ($tiktokScore >= 70) {
                $reasons[] = "Tiềm năng TikTok cao ({$tiktokScore}/100) nhờ định dạng phù hợp và nỗi đau khách hàng rõ ràng.";
            }
            if ($fbScore >= 70) {
                $reasons[] = "Tiềm năng Facebook Reels cao ({$fbScore}/100) với mức hoa hồng và nhu cầu thực tế tốt.";
            }
            if ($ytScore >= 70) {
                $reasons[] = "Tiềm năng YouTube Shorts cao ({$ytScore}/100) nhờ từ khóa tìm kiếm ngách tốt.";
            }
            if (!empty($prod['sales_count']) && $prod['sales_count'] > 500) {
                $reasons[] = "Đã có " . number_format($prod['sales_count']) . " lượt bán trên thị trường.";
            }
            if (!empty($prod['commission_rate']) && $prod['commission_rate'] >= 8) {
                $reasons[] = "Tỷ lệ hoa hồng hấp dẫn: {$prod['commission_rate']}%.";
            }
            $selectionReasonText = implode(' ', $reasons);

            // Check existing candidate
            $existing = $this->d->rawQueryOne(
                "SELECT id, status FROM table_content_candidate WHERE product_id = ? LIMIT 1",
                array($productId)
            );

            if (!empty($existing)) {
                $this->d->where('id', $existing['id']);
                $this->d->update('content_candidate', array(
                    'research_id' => !empty($prod['research_id']) ? (int)$prod['research_id'] : null,
                    'global_score' => $globalScore,
                    'tiktok_score' => $tiktokScore,
                    'facebook_score' => $fbScore,
                    'youtube_score' => $ytScore,
                    'target_platform' => $targetPlatform,
                    'selection_reason' => $selectionReasonText,
                    'eligibility_status' => $eligibility['status'],
                    'eligibility_reasons' => json_encode($eligibility['reasons'], JSON_UNESCAPED_UNICODE),
                    'cooldown_until' => $eligibility['cooldown_until'],
                    'date_updated' => $now
                ));
                $updatedCount++;
            } else {
                $this->d->insert('content_candidate', array(
                    'product_id' => $productId,
                    'research_id' => !empty($prod['research_id']) ? (int)$prod['research_id'] : null,
                    'global_score' => $globalScore,
                    'tiktok_score' => $tiktokScore,
                    'facebook_score' => $fbScore,
                    'youtube_score' => $ytScore,
                    'target_platform' => $targetPlatform,
                    'selection_reason' => $selectionReasonText,
                    'eligibility_status' => $eligibility['status'],
                    'eligibility_reasons' => json_encode($eligibility['reasons'], JSON_UNESCAPED_UNICODE),
                    'status' => self::STATUS_PENDING,
                    'cooldown_until' => $eligibility['cooldown_until'],
                    'created_by' => 'system_ranker',
                    'date_created' => $now,
                    'date_updated' => $now
                ));
                $createdCount++;
            }
        }

        return array(
            'success' => true,
            'scanned' => count($products),
            'candidates_created' => $createdCount,
            'candidates_updated' => $updatedCount,
            'skipped' => $skippedCount
        );
    }

    /**
     * Get Candidate List with Filters
     * @param array $filters ['status', 'platform', 'page', 'limit']
     * @return array
     */
    public function getCandidates(array $filters = array())
    {
        if (!$this->d) return array('items' => array(), 'total' => 0);

        $where = array("1 = 1");
        $params = array();

        if (!empty($filters['status'])) {
            $where[] = "c.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['platform']) && $filters['platform'] !== 'all') {
            $where[] = "(c.target_platform = ? OR c.target_platform = 'all')";
            $params[] = $filters['platform'];
        }

        $whereSql = implode(" AND ", $where);
        $page = max(1, (int)($filters['page'] ?? 1));
        $limit = min(100, max(1, (int)($filters['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;

        $totalRow = $this->d->rawQueryOne("SELECT COUNT(*) as total FROM table_content_candidate c WHERE {$whereSql}", $params);
        $total = (int)($totalRow['total'] ?? 0);

        $sql = "SELECT c.*, p.namevi as product_name, p.photo as product_photo, p.sale_price, p.regular_price, p.slugvi
                FROM table_content_candidate c
                LEFT JOIN table_product p ON c.product_id = p.id
                WHERE {$whereSql}
                ORDER BY c.global_score DESC, c.id DESC
                LIMIT {$offset}, {$limit}";

        $items = $this->d->rawQuery($sql, $params);

        return array(
            'items' => $items ?: array(),
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        );
    }

    /**
     * Approve Candidate for Content Package Generation
     * @param int $idCandidate
     * @param string $adminUser
     * @return array ['success' => bool, 'message' => string]
     */
    public function approveCandidate($idCandidate, $adminUser = 'admin')
    {
        if (!$this->d) return array('success' => false, 'message' => 'Lỗi kết nối CSDL');
        
        $cand = $this->d->rawQueryOne("SELECT * FROM table_content_candidate WHERE id = ? LIMIT 1", array((int)$idCandidate));
        if (empty($cand)) {
            return array('success' => false, 'message' => 'Không tìm thấy ứng viên nội dung.');
        }

        $this->d->where('id', (int)$idCandidate);
        $ok = $this->d->update('content_candidate', array(
            'status' => self::STATUS_APPROVED,
            'date_updated' => time()
        ));

        return array(
            'success' => (bool)$ok,
            'message' => $ok ? 'Đã duyệt ứng viên vào danh sách sẵn sàng tạo Content Package.' : 'Không thể cập nhật trạng thái.'
        );
    }

    /**
     * Discard Candidate
     * @param int $idCandidate
     * @param string $reason
     * @return array ['success' => bool, 'message' => string]
     */
    public function discardCandidate($idCandidate, $reason = '')
    {
        if (!$this->d) return array('success' => false, 'message' => 'Lỗi kết nối CSDL');

        $this->d->where('id', (int)$idCandidate);
        $ok = $this->d->update('content_candidate', array(
            'status' => self::STATUS_DISCARDED,
            'selection_reason' => trim(($cand['selection_reason'] ?? '') . ' [Loại bỏ: ' . $reason . ']'),
            'date_updated' => time()
        ));

        return array(
            'success' => (bool)$ok,
            'message' => $ok ? 'Đã loại bỏ ứng viên khỏi danh sách ưu tiên.' : 'Lỗi cập nhật.'
        );
    }
}
