<?php if (!empty(@$seoDB['schema' . $seolang])) { ?>
    <script type="application/ld+json">
        <?= htmlspecialchars_decode($seoDB['schema' . $seolang]) ?>
    </script>
<?php } ?>
<?php if ($template == 'static/static' && !empty($static)) { ?>
    <!-- Static Article Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Article",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "<?= $seo->get('url') ?>"
            },
            "headline": "<?= addslashes(@$static['name' . $lang]) ?>",
            "image": [
                "<?= $configBase . UPLOAD_NEWS_L . @$static['photo'] ?>"
            ],
            "datePublished": "<?= !empty($static['date_created']) ? date('c', $static['date_created']) : date('c') ?>",
            "dateModified": "<?= !empty($static['date_updated']) ? date('c', $static['date_updated']) : (!empty($static['date_created']) ? date('c', $static['date_created']) : date('c')) ?>",
            "author": {
                "@type": "Organization",
                "name": "Khỏe Pro",
                "url": "<?= $configBase ?>"
            },
            "publisher": {
                "@type": "Organization",
                "name": "Khỏe Pro",
                "logo": {
                    "@type": "ImageObject",
                    "url": "<?= $configBase . UPLOAD_PHOTO_L . @$logo['photo'] ?>"
                }
            },
            "description": "<?= addslashes($seo->get('description')) ?>"
        }
    </script>
<?php } ?>
<!-- Organization Schema -->
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?= !empty($setting['name' . $lang]) ? addslashes($setting['name' . $lang]) : 'Khỏe Pro' ?>",
        "url": "<?= $configBase ?>",
        "logo": "<?= $configBase . UPLOAD_PHOTO_L . @$logo['photo'] ?>",
        "sameAs": [
            <?php if (isset($social) && count($social) > 0) {
                $socArr = [];
                foreach ($social as $val) {
                    if (!empty($val['link'])) $socArr[] = '"' . $val['link'] . '"';
                }
                echo implode(',', $socArr);
            } ?>
        ],
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "<?= !empty($setting['address' . $lang]) ? addslashes($setting['address' . $lang]) : 'Hồ Chí Minh' ?>",
            "addressRegion": "Hồ Chí Minh",
            "postalCode": "70000",
            "addressCountry": "VN"
        }
    }
</script>