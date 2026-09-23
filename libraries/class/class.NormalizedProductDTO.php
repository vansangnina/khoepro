<?php
/**
 * KHOEPRO - Normalized Product Data Transfer Object (DTO)
 * Standardizes product representations across all affiliate sources & content engines.
 * PHP 7.4 & 8.x Compatible
 */

if (!defined('LIBRARIES')) {
    define('LIBRARIES', __DIR__ . '/../');
}

class NormalizedProductDTO
{
    public $externalId = '';
    public $provider = 'accesstrade';
    public $platform = 'other';
    public $title = '';
    public $name = '';
    public $slug = '';
    public $price = 0.0;
    public $originalPrice = 0.0;
    public $discountPct = 0.0;
    public $currency = 'VND';
    public $affiliateUrl = '';
    public $destinationUrl = '';
    public $imageUrl = '';
    public $extraImages = array();
    public $merchant = '';
    public $brand = '';
    public $category = 'Gym & Fitness';
    public $commissionRate = 0.0;
    public $commissionValue = 0.0;
    public $inStock = true;
    public $rating = 5.0;
    public $salesCount = 0;
    public $reviewCount = 0;
    public $specs = array();
    public $description = '';
    public $campaignId = '';
    public $rawData = array();

    public function __construct(array $data = array())
    {
        if (!empty($data)) {
            $this->fromArray($data);
        }
    }

    public function fromArray(array $data)
    {
        $this->externalId = (string)($data['external_id'] ?? ($data['product_id'] ?? ($data['id'] ?? '')));
        $this->provider = strtolower((string)($data['provider'] ?? ($data['source'] ?? 'accesstrade')));
        $this->platform = strtolower((string)($data['platform'] ?? ($data['merchant'] ?? 'other')));
        
        $rawTitle = trim((string)($data['title'] ?? ($data['name'] ?? ($data['namevi'] ?? ''))));
        $this->title = $rawTitle;
        $this->name = $rawTitle;
        $this->slug = (string)($data['slug'] ?? ($data['slugvi'] ?? ''));

        $this->price = (float)($data['price'] ?? ($data['sale_price'] ?? 0));
        $this->originalPrice = (float)($data['original_price'] ?? ($data['regular_price'] ?? $this->price));
        if ($this->originalPrice > $this->price && $this->originalPrice > 0) {
            $this->discountPct = round((($this->originalPrice - $this->price) / $this->originalPrice) * 100, 1);
        } else {
            $this->discountPct = (float)($data['discount_pct'] ?? ($data['discount'] ?? 0));
        }

        $this->currency = (string)($data['currency'] ?? 'VND');
        $this->affiliateUrl = (string)($data['affiliate_url'] ?? ($data['aff_url'] ?? ($data['url'] ?? '')));
        $this->destinationUrl = (string)($data['destination_url'] ?? ($data['original_url'] ?? ''));
        $this->imageUrl = (string)($data['image_url'] ?? ($data['image'] ?? ($data['photo'] ?? '')));
        $this->extraImages = is_array($data['extra_images'] ?? null) ? $data['extra_images'] : array();

        $this->merchant = (string)($data['merchant'] ?? ($data['seller_name'] ?? ($data['at_merchant'] ?? '')));
        $this->brand = (string)($data['brand'] ?? '');
        $this->category = (string)($data['category'] ?? ($data['category_name'] ?? 'Gym & Fitness'));

        $this->commissionRate = (float)($data['commission_rate'] ?? 0);
        $this->commissionValue = (float)($data['commission_value'] ?? 0);
        if ($this->commissionValue <= 0 && $this->commissionRate > 0 && $this->price > 0) {
            $this->commissionValue = round($this->price * ($this->commissionRate / 100), 2);
        }

        $this->inStock = !isset($data['in_stock']) || (bool)$data['in_stock'];
        $this->rating = isset($data['rating']) ? (float)$data['rating'] : 5.0;
        $this->salesCount = (int)($data['sales_count'] ?? ($data['sales'] ?? ($data['sold_count'] ?? 0)));
        $this->reviewCount = (int)($data['review_count'] ?? 0);
        
        $this->specs = is_array($data['specs'] ?? null) ? $data['specs'] : (!empty($data['specs']) ? array('specs' => (string)$data['specs']) : array());
        $this->description = (string)($data['description'] ?? ($data['descvi'] ?? ($data['desc'] ?? '')));
        $this->campaignId = (string)($data['campaign_id'] ?? ($data['at_campaign_id'] ?? ''));
        $this->rawData = is_array($data['raw_data'] ?? null) ? $data['raw_data'] : ($data['raw'] ?? array());

        return $this;
    }

    public function toArray()
    {
        return array(
            'external_id' => $this->externalId,
            'provider' => $this->provider,
            'platform' => $this->platform,
            'title' => $this->title,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'original_price' => $this->originalPrice,
            'discount_pct' => $this->discountPct,
            'currency' => $this->currency,
            'affiliate_url' => $this->affiliateUrl,
            'destination_url' => $this->destinationUrl,
            'image_url' => $this->imageUrl,
            'extra_images' => $this->extraImages,
            'merchant' => $this->merchant,
            'brand' => $this->brand,
            'category' => $this->category,
            'commission_rate' => $this->commissionRate,
            'commission_value' => $this->commissionValue,
            'in_stock' => $this->inStock,
            'rating' => $this->rating,
            'sales_count' => $this->salesCount,
            'review_count' => $this->reviewCount,
            'specs' => $this->specs,
            'description' => $this->description,
            'campaign_id' => $this->campaignId,
            'raw_data' => $this->rawData
        );
    }

    public function toJson($options = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Validate product eligibility for content creation
     * @return array ['valid' => bool, 'errors' => array()]
     */
    public function validateForContent()
    {
        $errors = array();
        if (empty($this->name)) {
            $errors[] = 'Tên sản phẩm không được để trống';
        }
        if (empty($this->affiliateUrl)) {
            $errors[] = 'Đường dẫn affiliate (URL) không tồn tại';
        }
        if (empty($this->imageUrl)) {
            $errors[] = 'Hình ảnh sản phẩm không tồn tại';
        }
        if ($this->price <= 0) {
            $errors[] = 'Giá sản phẩm không hợp lệ (<= 0)';
        }
        if (!$this->inStock) {
            $errors[] = 'Sản phẩm hiện đang hết hàng';
        }

        return array(
            'valid' => empty($errors),
            'errors' => $errors
        );
    }
}
