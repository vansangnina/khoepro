# FITNADO DESIGN SYSTEM (HỆ THỐNG THIẾT KẾ)

Tài liệu này được chuẩn hóa từ file prototype Desktop (`fitnado-desktop.html`), Mobile (`fitnado-mobile.html`) và CSS hệ thống (`assets/css/fitnado.css`).

---

## 1. MÀU SẮC (COLOR TOKENS)

```css
:root {
    /* Brand Colors */
    --b: #0256aa;         /* Primary Blue - Màu nhận diện thương hiệu FITNADO */
    --bd: #01478f;        /* Primary Dark - Hover states, dark accents */
    --b-light: #eef5fc;   /* Light Blue Tint - Background highlights */
    
    /* Neutral & Backgrounds */
    --bg: #f5f8fc;        /* Light Grayish Blue - Card & section background */
    --bg-phone: #eef3f8;  /* Mobile app background frame */
    --t: #111827;         /* Heading & Body Text (Dark Charcoal) */
    --m: #667085;         /* Muted Text / Secondary Labels (Cool Gray) */
    --line: #e5eaf0;      /* Border / Divider Line */
    
    /* Accents */
    --star: #f59e0b;      /* Rating Stars (Amber Gold) */
    --danger: #ef4444;    /* Discount badge, sale alerts */
    --success: #10b981;   /* Stock status, verified badges */
}
```

### Gradients Chuẩn
* **Hero Desktop Gradient**: `linear-gradient(90deg, #f5f9ff 0%, #eef5fc 58%, #dceaf8 100%)`
* **Hero Visual Dark Gradient**: `linear-gradient(135deg, #0c2944, #0256aa)`
* **Hero Mobile Gradient**: `linear-gradient(135deg, #06437c, #0256aa)`
* **Newsletter Banner Gradient**: `linear-gradient(110deg, #01478f, #0876d9)`
* **Placeholder Media Gradient**: `linear-gradient(145deg, #e9eef4, #cdd8e4)`
* **Video Dark Gradient**: `linear-gradient(160deg, #132d44, #507ca5)`

---

## 2. TYPOGRAPHY (KIỂU CHỮ)

* **Font Family**: `Arial, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`
* **Logo Typography**:
  * Brand Name: `font-size: 32px; font-weight: 900; font-style: italic; color: var(--b); letter-spacing: -0.5px;`
  * Sub-slogan: `font-size: 8px; font-weight: 700; color: #344054; letter-spacing: 1px; text-transform: uppercase;`
* **Headings**:
  * Hero Title (Desktop): `font-size: 54px; line-height: 0.98; font-weight: 900;`
  * Hero Title (Mobile): `font-size: 34px; line-height: 1.0; font-weight: 900;`
  * Section Title (Desktop): `font-size: 25px; font-weight: 800;`
  * Section Title (Mobile): `font-size: 20px; font-weight: 800;`
  * Product Title: `font-size: 15px; font-weight: 700; line-height: 1.3;`

---

## 3. KHÔNG GIAN & BỐ CỤC (LAYOUT & SPACING)

* **Max Width Wrapper (Desktop)**: `max-width: 1440px; margin: auto; padding: 0 42px;`
* **Mobile Frame Max Width**: `max-width: 430px; margin: auto;`
* **Breakpoints**:
  * Desktop Large: `1440px`
  * Desktop Standard: `1200px`
  * Tablet Landscape: `992px`
  * Tablet Portrait / Mobile Landscape: `768px`
  * Mobile Standard: `430px`, `390px`, `375px`, `360px`

---

## 4. COMPONENTS CHUẨN (UI COMPONENTS)

### 1. Button (`.btn`, `.ghost`)
* **Primary Button**: `background: var(--b); color: #fff; padding: 13px 22px; border-radius: 7px; font-weight: 700; border: 0; cursor: pointer;`
* **Ghost Button**: `background: #fff; color: var(--b); border: 1px solid var(--b);`

### 2. Category Item (`.cat`)
* Desktop: Grid 8 cột (`repeat(8, 1fr)`), bo góc `12px`, shadow nhẹ `0 4px 14px #0b4b8410`, viền `1px solid var(--line)`.
* Mobile: Horizontal Scroll (`overflow-x: auto; display: flex; gap: 10px; min-width: 82px;`).

### 3. Product Card (`.card`)
* Bo góc: `12px`, tràn viền, viền `1px solid var(--line)`.
* Tỉ lệ ảnh (`.pic`): chiều cao `175px` (desktop), `160px` (mobile), tỉ lệ chuẩn `1:1` hoặc `4:3`.
* Giá bán (`.price`): `color: var(--b); font-size: 20px; font-weight: 900;`.
* Rating (`.rating`): `color: var(--star); font-weight: 700;`.

### 4. Video Short Card (`.video`)
* Desktop: Grid 6 cột (`repeat(6, 1fr)`).
* Mobile: Horizontal Scroll Carousel.
* Aspect Ratio: Dọc chuẩn 9:16 hoặc 3:4 (chiều cao `210px` - `230px`).

### 5. Goal Box (`.goal`)
* Background gradient xanh đậm: `linear-gradient(135deg, #0b2135, #0256aa)`.
* Chiều cao: `105px` - `110px`, chữ in hoa màu trắng `font-size: 18px-20px; font-weight: 900;`.

### 6. Comparison Table (`.compare`)
* Cột 3 phân đoạn: Sản phẩm A (1fr) - Ma trận tiêu chí (1.7fr) - Sản phẩm B (1fr).

### 7. Bottom Navigation Mobile (`.bottom`)
* Cố định dưới chân màn hình (`position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: min(430px, 100%); z-index: 8;`).
* 5 tab chức năng: Trang chủ, Danh mục, Video, Yêu thích, Tài khoản.
