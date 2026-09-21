# KHOEPRO.COM — GOOGLE SEARCH CONSOLE & PRE/POST-LAUNCH INDEXATION CHECKLIST

> **Target Domain:** `https://khoepro.com`
> **Target Brand:** `Khỏe Pro`
> **Document Version:** 1.0 (Pre-Launch Ready)

## PHASE 1: PRE-LAUNCH LOCAL VERIFICATION (COMPLETED)

- [x] **Zero Development Domain Leaks**: Verified zero occurrences of `fitnado.local`, `localhost`, `127.0.0.1` in HTML, metadata, canonical, or sitemap.
- [x] **404 Handling**: 11 non-existent URLs tested and verified returning HTTP 404 with `noindex,follow`.
- [x] **Soft 404 Elimination**: Empty categories, missing IDs, out-of-range paginations return clean HTTP 404 instead of HTTP 200.
- [x] **Internal Links**: 100% crawlable internal links return valid HTTP 200 (0 broken links).
- [x] **Assets & Images**: 100% frontend image assets exist and load without 404 or broken references.
- [x] **Structured Data**: Organization, BreadcrumbList, and Article JSON-LD verified valid with zero fake ratings.
- [x] **PHP 7.4 Runtime**: Tested with real PHP 7.4 binary (`/Applications/MAMP/bin/php/php7.4.33/bin/php`).

## PHASE 2: PRODUCTION SERVER DEPLOYMENT (POST-UPLOAD)

> [!IMPORTANT]
> Perform these checks immediately after uploading source files and importing database on the live host.

### 1. SSL & Protocol Verification
- [ ] `https://khoepro.com` loads with valid SSL certificate (green padlock).
- [ ] `http://khoepro.com` returns `301 Moved Permanently` to `https://khoepro.com`.
- [ ] `http://www.khoepro.com` returns `301 Moved Permanently` to `https://khoepro.com`.
- [ ] `https://www.khoepro.com` returns `301 Moved Permanently` to `https://khoepro.com`.

### 2. Live File Verification
- [ ] Test `https://khoepro.com/robots.txt` in browser. Ensure sitemap directive points to `https://khoepro.com/sitemap.xml`.
- [ ] Test `https://khoepro.com/sitemap.xml` in browser. Ensure all 80 URLs are present and formatted with `https://khoepro.com/`.
- [ ] Test `https://khoepro.com/kiem-tra-404-ngau-nhien` in browser. Ensure HTTP response header is `404 Not Found`.

## PHASE 3: GOOGLE SEARCH CONSOLE SUBMISSION

### 1. Property Setup & Ownership Verification
- [ ] Log into [Google Search Console](https://search.google.com/search-console).
- [ ] Add Property -> **Domain Property**: `khoepro.com`.
- [ ] Verify ownership via DNS TXT record (recommended) or HTML tag.

### 2. Sitemap Submission
- [ ] Navigate to **Sitemaps** section in GSC.
- [ ] Enter `sitemap.xml` and click **Submit**.
- [ ] Confirm Status displays **Success** with **80 discovered URLs**.

### 3. Key URL Live Inspection & Indexing Request
Perform URL Inspection on the following representative sample URLs:
- [ ] **Homepage**: `https://khoepro.com/`
- [ ] **Product Category**: `https://khoepro.com/dai-lung-tap-gym`
- [ ] **Product Detail**: `https://khoepro.com/dai-lung-da-harbinger-4-inch-padded-leather-belt`
- [ ] **Review Detail**: `https://khoepro.com/danh-gia-dai-lung-da-harbinger-4-inch`
- [ ] **Buying Guide**: `https://khoepro.com/huong-dan-chon-dai-lung-tap-gym`
- [ ] **Comparison**: `https://khoepro.com/so-sanh-dai-lung-don-bay-va-dai-cai-chot`
- [ ] **Static Policy**: `https://khoepro.com/phuong-phap-danh-gia`

## PHASE 4: SOCIAL MEDIA & RICH RESULTS TEST

- [ ] Test Homepage and Article URLs on [Facebook Sharing Debugger](https://developers.facebook.com/tools/debug/) to verify OG 1200x630 thumbnail caching.
- [ ] Test Article and Breadcrumb schemas on [Google Rich Results Test](https://search.google.com/test/rich-results).
