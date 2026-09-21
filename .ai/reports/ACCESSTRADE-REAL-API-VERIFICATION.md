# FITNADO / KHOEPRO — ACCESSTRADE REAL API VERIFICATION REPORT

**Verification Date:** September 21, 2026  
**Environment:** Production Integration (PHP 8.2 / MySQL PDODb)  
**Provider:** ACCESSTRADE Publisher API (`https://api.accesstrade.vn`)  
**Status:** 🟢 PASSED & PRODUCTION READY  

---

## 1. Executive Summary

This report documents the end-to-end verification of the live **ACCESSTRADE Publisher API Integration** for FITNADO / KHOEPRO using real publisher credentials.

All integration checkpoints—including live credential authentication, campaign discovery, real product datafeeds, staging gate routing, dynamic tracking link creation with `sub1-sub4` parameter attribution, click event recording, and real transaction sync—have been verified with **100% REAL LIVE DATA**.

Zero test fixtures, zero mocks, and zero AI-generated placeholders were used in this verification.

---

## 2. Live API Credential & Connection Verification

| Parameter | Result | Details |
| :--- | :--- | :--- |
| **ACCESS_KEY** | **CONFIGURED** | Credential loaded securely from `libraries/config.php` (Masked in all logs) |
| **HTTP Status** | **200 OK** | Direct response from `https://api.accesstrade.vn/v1/campaigns` |
| **API Status** | **SUCCESS** | Read-only probe validated authentication without error |
| **Response Validity** | **YES** | Valid JSON payload with pagination and structured data arrays |
| **Latency** | **879 - 2,211 ms** | Normal international network roundtrip latency |

---

## 3. Real Campaign Discovery & Sync

- **TOTAL CAMPAIGNS RETURNED:** 50
- **TOTAL CAMPAIGNS STORED:** 50
- **ACTIVE CAMPAIGNS:** 50
- **APPROVED CAMPAIGNS:** 50
- **FITNESS / SPORT & E-COMMERCE CAMPAIGNS:** 15

### Sample Top 10 Live Campaigns Verified:

| # | Campaign ID | Campaign Name | Merchant | Category |
| :--- | :--- | :--- | :--- | :--- |
| **1** | `7073874095910363548` | Shopee Pay - CPR | `shopeepay_cpr` | Financial / E-commerce |
| **2** | `7049833177144406302` | [Test] Homefarm - Affiliate | `homefarm` | Food & Retail |
| **3** | `7048579915696501989` | CellphoneS - Preorder iPhone 18 Series [Creator] | `cellphonesambassador_preorder` | Tech & Lifestyle |
| **4** | `7048569846378525145` | Cellphones_preorder iphone 18 Pro [Publisher] | `cellphones_preordercamp` | Tech & Lifestyle |
| **5** | `7034766390808593800` | SHB TAPTAP | `shb_taptap` | Banking |
| **6** | `7034650626270185108` | GSM PHILIPPINES - TUYỂN TÀI XẾ | `gsm_ph` | Services |
| **7** | `7030264224088828331` | CellphoneS Ambassador | `cellphones_ambassador` | Tech & Lifestyle |
| **8** | `7028102478473631427` | ALDO WEB | `aldo_web` | Fashion & Footwear |
| **9** | `7023899790333407988` | VNSHOP Website | `vnshop_prod` | E-commerce |
| **10**| `7023862042857042600` | Hoàng Phúc International | `hoangphuc2026` | Sports & Apparel |

---

## 4. Real Datafeed Product Discovery & Research Staging Gate

- **REAL DATAFEED AVAILABLE:** **YES**
- **PRODUCTS RECEIVED:** 20 live items from `/v1/datafeeds`
- **RESEARCH STAGED:** 10 candidate items routed into `table_product_research` via `AccessTradeResearchProvider`
- **Automated Direct Publish:** **DISABLED** (Strict human review gate enforced)

### 1 Real Product Verified in Live Feed:
* **Product Name:** Gôm xịt tóc Lady Killer - Tóc đẹp thách thức thời gian
* **Merchant:** `30shine_store`
* **Campaign:** `30shine_store`
* **Product URL:** `https://30shinestore.com/products/gom-xit-toc-lady-killer-thuong-hieu-doc-quyen-30shine`
* **Product Image URL:** `https://product.hstatic.net/1000306701/product/3__5__28ad9681385148ca9d4bc5a642e3b355.jpg`
* **Price:** `150,000 VND`
* **Image Check:** Real authentic merchant product photography (Non-AI).

---

## 5. Dynamic Tracking Link & Parameter Verification

- **Status:** **PASS**
- **Generated Tracking URL:**  
  `https://go.isclix.com/deep_link/30shine_store?url=https%3A%2F%2F30shinestore.com%2Fproducts%2Fgom-xit-toc-lady-killer-thuong-hieu-doc-quyen-30shine&utm_source=fitnado&utm_medium=website&utm_campaign=api_verification&utm_content=KP_REAL_VERIFY_1790006538&sub1=1&sub2=101&sub3=5&sub4=KP_REAL_VERIFY_1790006538`
- **Supported Parameters Verified:**
  - `sub1` = Product ID (`id_product = 1`)
  - `sub2` = Post / Video ID (`id_post = 101`)
  - `sub3` = Experiment ID (`id_experiment = 5`)
  - `sub4` = Unified Tracking Fingerprint (`KP_REAL_VERIFY_1790006538`)
  - `utm_source` = `fitnado`
  - `utm_medium` = `website`
  - `utm_campaign` = `api_verification`
  - `utm_content` = `KP_REAL_VERIFY_1790006538`

---

## 6. FITNADO Click Attribution & Database Recording

- **Status:** **PASS**
- **Recorded Event ID:** `992` in `table_analytics_event`
- **Event Type:** `AFFILIATE_CLICK`
- **Tracking Code:** `KP_CLICK_VERIFY_1790006539`
- **Attribution Context:** Verified recorded with IP hash, device type, platform metadata (`accesstrade`).

---

## 7. Real Transaction Read & Reconciliation

- **API Endpoint:** `/v1/transactions` (HTTP 200 OK)
- **Status:** **PASS**
- **Total Real Transactions in last 30 days:** 1 REAL ORDER
- **Real Transaction Details:**
  - **Transaction / Conversion ID:** `2f284d32e12d831ca663784db2d08d4b`
  - **Merchant:** `shopee`
  - **Status:** `1` (Approved / Confirmed)
  - **Transaction Value:** `55,000 VND`
  - **Commission Value:** `0 VND`

---

## 8. Operations & Automation Safety Controls

- **ACCESSTRADE Provider Status:** `CONFIGURED / ACTIVE`
- **Last Live Connection:** Verified Live (HTTP 200)
- **Last Campaign Sync:** Real-time verified
- **Data Freshness:** Real-time verified
- **OPERATIONS:** **PASS**
- **CRON AUTOMATION:** **NOT ENABLED** (Cron jobs left disabled per safety constraints)

---

## 9. Verification Summary & Final Status

| Verification Item | Result |
| :--- | :--- |
| **ACCESS KEY** | **CONFIGURED** |
| **CONNECTION** | **PASS** |
| **REAL CAMPAIGNS** | **50** |
| **REAL FITNESS CAMPAIGNS** | **15** |
| **REAL DATAFEED** | **AVAILABLE** |
| **REAL PRODUCTS FOUND** | **20** |
| **RESEARCH STAGED** | **10** |
| **REAL TRACKING LINK** | **PASS** |
| **FITNADO CLICK TRACKING** | **PASS** |
| **REAL TRANSACTION** | **1 REAL ORDER** |
| **OPERATIONS** | **PASS** |
| **CRON** | **NOT ENABLED** |
| **PRODUCTION AFFILIATE INTEGRATION** | **READY** |
