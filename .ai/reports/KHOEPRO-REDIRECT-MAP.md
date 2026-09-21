# KHOEPRO.COM — PRODUCTION REDIRECT MAP & CANONICAL NORMALIZATION

> **Domain:** `https://khoepro.com`
> **Redirect Architecture:** Single-hop 301 Permanent Redirects
> **Loop / Chain Target:** 0 chains, 0 loops

## 1. Domain & Protocol Normalization Rules

| SOURCE URL PATTERN | STATUS | DESTINATION URL | REASON | CHAIN LENGTH |
| :--- | :-: | :--- | :--- | :-: |
| `http://khoepro.com/*` | 301 | `https://khoepro.com/*` | Enforce HTTPS encryption | 1 hop |
| `http://www.khoepro.com/*` | 301 | `https://khoepro.com/*` | Enforce HTTPS & non-WWW root | 1 hop |
| `https://www.khoepro.com/*` | 301 | `https://khoepro.com/*` | Enforce non-WWW root | 1 hop |
| `https://khoepro.com/index.php` | 301 | `https://khoepro.com/` | Default document duplicate normalization | 1 hop |
| `https://khoepro.com/home` | 301 | `https://khoepro.com/` | Default homepage alias normalization | 1 hop |
| `https://khoepro.com/trang-chu` | 301 | `https://khoepro.com/` | Default homepage alias normalization | 1 hop |

## 2. Legacy / Alias Route Redirection Map

| SOURCE URL | STATUS | DESTINATION URL | REASON | FINAL STATUS | CHAIN LENGTH |
| :--- | :-: | :--- | :--- | :-: | :-: |
| `/danh-gia` | 301 | `/danh-gia-review` | Standardized review category hub slug | 200 | 1 hop |
| `/huong-dan` | 301 | `/huong-dan-chon-mua` | Standardized buying guide category hub slug | 200 | 1 hop |
| `/kien-thuc` | 301 | `/kien-thuc-tap-luyen` | Standardized knowledge category hub slug | 200 | 1 hop |
| `/chinh-sach` | 301 | `/phuong-phap-danh-gia` | Policy directory default redirect | 200 | 1 hop |

## 3. Server Configuration Implementation (.htaccess snippet)

```apache
# Enforce HTTPS and non-WWW
RewriteCond %{HTTPS} off [OR]
RewriteCond %{HTTP_HOST} ^www\.khoepro\.com$ [NC]
RewriteRule ^(.*)$ https://khoepro.com/$1 [L,R=301]

# Prevent /index.php duplication
RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /index\.php\ HTTP/
RewriteRule ^index\.php$ https://khoepro.com/ [R=301,L]
```
