# BÁO CÁO BENCHMARK GIỌNG ĐỌC TIẾNG VIỆT TỰ NHIÊN (PHASE 06.4)
## NATURAL VIETNAMESE VOICE BENCHMARK FOR FITNADO VIDEO ENGINE

- **Thời gian thực hiện:** 2026-09-20
- **Dự án:** FITNADO Video Production Engine (Phase 06.4)
- **Tập tin kịch bản kiểm thử:** Kịch bản TikTok đã duyệt (Approved Script) — Sản phẩm Đai Cứng FITNADO Pro Lever (Product ID 80)
- **Trạng thái phê duyệt:** `HUMAN VOICE SELECTION: PENDING` (Chờ Developer nghe thẩm định và lựa chọn)

---

## 1. AUDIT HIỆN TRẠNG GIỌNG ĐỌC (CURRENT VOICE AUDIT)

Xác định chính xác thông số kỹ thuật của giọng đọc đang được sử dụng trước Phase 06.4 trong mã nguồn `VideoComposer.php`:

| Thuộc tính | Giá trị hiện tại | Ghi chú kỹ thuật |
| :--- | :--- | :--- |
| **Provider** | `Google Translate TTS` | Endpoint công khai `tw-ob` |
| **Model** | `tw-ob` (Translate Web Open Broadcast) | Cố định, không có model tùy biến |
| **Voice** | `vi-VN-Standard` | Giọng nữ đọc máy Google Tiếng Việt |
| **Language** | `vi` | Tiếng Việt |
| **Speed** | `1.0` (Cố định) | Không hỗ trợ điều chỉnh tốc độ |
| **Pitch** | `Default` (Cố định) | Không hỗ trợ điều chỉnh cao độ |
| **Format** | `MP3` (`audio/mpeg`) | Audio mono |
| **Sample Rate** | `24,000 Hz` | Bitrate 64 kbps |
| **Đánh giá chất lượng** | Giọng đọc đều đều, thiếu nhịp thở tự nhiên, phát âm sai tên thương hiệu `FITNADO` (đọc từng chữ cái F-I-T...) và các thuật ngữ Gym (`Squat`, `Deadlift`, `PR`, `150kg`), phong cách dạng tin tức máy móc, không phù hợp với video ngắn TikTok Creator/Reviewer. |

---

## 2. BÁO CÁO KHẢO SÁT NĂNG LỰC BEEKNOEE TTS

Hệ thống đã thực hiện kiểm tra và gửi yêu cầu API thực tế đến nền tảng Beeknoee (`https://platform.beeknoee.com`):

- **Endpoint chính thức:** `POST /v1/audio/speech` (OpenAI Audio Speech Standard API)
- **Các TTS Models khả dụng & Đã kiểm thử thành công:**
  1. `openai/tts-1-hd`: Model Speech HD chất lượng cao nhất, âm sắc chi tiết, hỗ trợ tiếng Việt mượt mà.
  2. `openai/tts-1`: Model Speech Standard, phản hồi nhanh.
  3. `google/gemini-2.5-flash-tts`: Model Gemini Flash đa ngôn ngữ, chi phí thấp.
  4. `google/gemini-3.1-flash-tts-preview`: Model Gemini thế hệ mới.
  5. `google/gemini-2.5-pro-tts`: Model Gemini Pro phát âm chi tiết (WAV).
  6. `openai/gpt-4o-mini-tts`: Model GPT-4o Mini TTS.
- **Danh sách Voices hỗ trợ:**
  - **Nữ:** `nova` (Năng động, cuốn hút, chuẩn Creator Reviewer), `shimmer` (Ấm áp, truyền cảm), `coral` (Trẻ trung, tươi tắn), `sage` (Điềm tĩnh, trang trọng).
  - **Nam:** `onyx` (Trầm ấm, uy lực, rất hợp thể thao/Gym), `echo` (Cân bằng, trôi chảy), `ash` (Thân thiện, tự nhiên), `fable` (Kể chuyện).
  - **Trung tính:** `alloy` (Thương mại tiêu chuẩn).
- **Hỗ trợ Tốc độ (Speed Control):** Dải tốc độ từ `0.25` đến `4.0` (Hỗ trợ chuẩn xác các mốc Benchmark: `0.95`, `1.00`, `1.05`).
- **Định dạng xuất:** `mp3`, `opus`, `aac`, `flac`, `wav`.
- **SSML / Emotion:** Không hỗ trợ thẻ SSML tùy tiện; ngữ điệu và nhịp thở được điều khiển chính xác thông qua cấu trúc dấu câu (`...`, `,`, `.`, `!`, `?`) và bộ tiền xử lý **Voice Preparation Engine**.

---

## 3. KIẾN TRÚC VOICE PROVIDER ABSTRACTION & VOICE PREPARATION

Hệ thống đã loại bỏ hoàn toàn việc gọi trực tiếp TTS trong `VideoComposer`, phân tách thành kiến trúc phân tầng chuyên nghiệp:

```text
VideoComposer
     ↓
VoiceService (Preparation, Caching, Normalization, Pronunciation Override)
     ↓
VoiceProviderInterface (synthesize, getVoices, getCapabilities)
     ├── GoogleTranslateVoiceProvider (Baseline / Free)
     ├── BeeknoeeVoiceProvider (OpenAI HD & Gemini TTS)
     └── MockVoiceProvider (Offline CI Testing)
```

### 3.1. Voice Preparation Engine (Viết cho Tai Nghe)
Trước khi gửi văn bản tới TTS Provider, văn bản kịch bản được chuyển đổi qua `prepareSpokenScript()`:

1. **Chuẩn hóa Giá tiền (Price Normalization):**
   - `399.000đ` / `399.000 VNĐ` → `ba trăm chín mươi chín nghìn đồng`
   - `500k` → `năm trăm nghìn đồng`
2. **Chuẩn hóa Số & Đơn vị (Number & Unit Normalization):**
   - `150kg` → `một trăm năm mươi cân`
   - `1.5kg` → `một phẩy năm cân`
   - `10mm` → `mười mi li mét`
   - `80%` → `tám mươi phần trăm`
   - `10-15 lần` / `8-12 rep` → `mười đến mười lăm lần` / `tám đến mười hai Rép`
   - `2 năm` → `hai năm`, `1 giây` → `một giây`
3. **Từ điển Phiên âm Thương hiệu & Thuật ngữ Gym (Pronunciation Dictionary):**
   - `FITNADO` → `Phít na đô`
   - `TikTok` → `Tíc tóc`
   - `Squat` → `S quat`
   - `Deadlift` → `Đét líp`
   - `Quick-Lock` → `Quích lóc`
   - `Pro Lever` → `Pờ rô Le vơ`
   - `PR` → `Pi A` (Kỷ lục cá nhân)
   - `Cardio` → `Cạc đi ô`
   - `Powerlifting` → `Pao oa líp tinh`
   - `Resistance Band` → `Dây kháng lực`
   - `Whey` → `Uây`, `Creatine` → `Cờ ri a tin`
   - *(Admin có thể chỉnh sửa/thêm từ ngữ trong giao diện Cài đặt AI Video)*.
4. **Nhịp ngắt đàm thoại (Punctuation Pacing):**
   - Tách các cụm 4–12 từ kết hợp dấu phẩy `,`, dấu chấm lửng `...` để giọng đọc có nhịp lấy hơi tự nhiên như người thật.

---

## 4. BẢNG SO SÁNH 3 MẪU ÂM THANH THẬT (VOICE A / B / C)

Cả 3 mẫu âm thanh được tạo ra từ **CHÍNH XÁC CÙNG MỘT NỘI DUNG** kịch bản TikTok đã duyệt (Đai Cứng FITNADO Pro Lever).

### Kịch bản gốc (Approved TikTok Script):
> *"Squat trên 150kg mà dùng đai dán mỏng là sai lầm nguy hiểm nhất! Đổi ngay sang Đai Cứng FITNADO Pro Lever khóa đòn bẩy hợp kim nguyên khối. Gạt khóa siết chặt tối đa, nén áp suất bụng cực đại giữ thẳng lưng. Bấm ngay giỏ hàng bên dưới nhận bảo hành khóa bẩy 2 năm trọn đời!"*

### Văn bản đọc thực tế sau Voice Preparation (Spoken Script):
> *"S quat trên một trăm năm mươi cân mà dùng đai dán mỏng là sai lầm nguy hiểm nhất! Đổi ngay sang Đai Cứng Phít na đô Pờ rô Le vơ khóa đòn bẩy hợp kim nguyên khối. Gạt khóa siết chặt tối đa, nén áp suất bụng cực đại giữ thẳng lưng. Bấm ngay giỏ hàng bên dưới nhận bảo hành khóa bẩy hai năm trọn đời!"*

---

### Bảng đối chiếu chi tiết 3 Samples:

| Thông số | SAMPLE A (Baseline) | SAMPLE B (Creator Female) | SAMPLE C (Gym Reviewer Male) |
| :--- | :--- | :--- | :--- |
| **Mục đích** | `BASELINE` (Hiện trạng cũ) | Giọng Nữ năng động / TikTok Reviewer | Giọng Nam thể thao / Uy lực tự tin |
| **Provider** | `Google Translate` | `Beeknoee AI` | `Beeknoee AI` |
| **Model** | `tw-ob` | `openai/tts-1-hd` | `openai/tts-1-hd` |
| **Voice ID** | `vi-VN-Standard` | `nova` | `onyx` |
| **Speed** | `1.00x` | `1.00x` | `1.00x` |
| **Thời lượng thực tế (ffprobe)** | **`23.47s`** | **`19.92s`** | **`19.75s`** |
| **Định dạng & Bitrate** | MP3 mono, 24kHz, 64 kbps | MP3 mono, 24kHz, 128 kbps (HD) | MP3 mono, 24kHz, 128 kbps (HD) |
| **Dung lượng file** | 187,776 bytes (~183 KB) | 318,720 bytes (~311 KB) | 316,032 bytes (~308 KB) |
| **Số ký tự (Characters)** | 297 ký tự | 297 ký tự | 297 ký tự |
| **Chi phí API (Cost)** | **0 VND** | **222.75 VND** (~$0.0089) | **222.75 VND** (~$0.0089) |
| **Tệp âm thanh dự án** | [`upload/audio/voice_A.mp3`](file:///e:/khoepro/upload/audio/voice_A.mp3) | [`upload/audio/voice_B.mp3`](file:///e:/khoepro/upload/audio/voice_B.mp3) | [`upload/audio/voice_C.mp3`](file:///e:/khoepro/upload/audio/voice_C.mp3) |
| **Tệp âm thanh Artifact** | [`voice_A.mp3`](file:///C:/Users/VanSang/.gemini/antigravity-ide/brain/8656d92f-5ca3-4047-822b-39b784afff59/voice_A.mp3) | [`voice_B.mp3`](file:///C:/Users/VanSang/.gemini/antigravity-ide/brain/8656d92f-5ca3-4047-822b-39b784afff59/voice_B.mp3) | [`voice_C.mp3`](file:///C:/Users/VanSang/.gemini/antigravity-ide/brain/8656d92f-5ca3-4047-822b-39b784afff59/voice_C.mp3) |
| **Trạng thái Thẩm định** | **`READY FOR HUMAN REVIEW`** | **`READY FOR HUMAN REVIEW`** | **`READY FOR HUMAN REVIEW`** |

---

## 5. TRẠNG THÁI CHUẨN HÓA PHÁT ÂM (NORMALIZATION STATUS)

- **Phát âm Tiếng Việt (Vietnamese Pronunciation):** `HOÀN THÀNH` — Ngắt nhịp câu chuẩn xác, nhấn trọng âm ở các lợi ích cốt lõi và cảnh báo giới hạn.
- **Phát âm Thương hiệu & Thuật ngữ Gym (Brand & Fitness Terms):** `HOÀN THÀNH` — Tự động phiên âm `FITNADO` → `Phít na đô`, `Squat` → `S quat`, `Deadlift` → `Đét líp`, `Pro Lever` → `Pờ rô Le vơ`, `PR` → `Pi A`.
- **Chuẩn hóa Số, Đơn vị & Giá tiền (Numbers/Units/Prices):** `HOÀN THÀNH` — Đọc chuẩn xác dạng văn nói tự nhiên (`150kg` → `một trăm năm mươi cân`, `2 năm` → `hai năm`, `399.000đ` → `ba trăm chín mươi chín nghìn đồng`).

---

## 6. TÍNH NĂNG ADMIN LIVE VOICE PREVIEW & AUDIO CACHE

- **Live Voice Preview:** Đã tích hợp trực tiếp trên trang Cài đặt AI Video (`admin/templates/ai_video/settings_tpl.php`). Admin có thể nhập bất kỳ câu thoại nào, chọn Voice Provider, Voice và Tốc độ, bấm **"Thử giọng ngay"** để nghe trực tiếp qua Audio Player tích hợp mà không cần render video.
- **Audio Caching:** Cache Key duy nhất theo `md5(provider | model | voice | md5(spoken_script) | speed | format)`. Mọi lần gọi lại kịch bản cũ sẽ tái sử dụng file âm thanh trong `upload/audio/`, đảm bảo **Chi phí = 0 VND**.
- **Video & Caption Sync:** `VideoComposer` tự động lấy thời lượng âm thanh thực tế từ `ffprobe` (`$voiceDuration`) để co giãn độ dài phân cảnh (`actualSceneDuration = max($baseDuration, $voiceDuration + 0.4)`), đảm bảo phụ đề và hình ảnh luôn khớp chính xác từng giây với giọng đọc.

---

## 7. QUYẾT ĐỊNH CỦA DEVELOPER (HUMAN VOICE SELECTION)

```text
HUMAN VOICE SELECTION: VOICE C (Onyx HD — Nam Gym Reviewer)
STATUS: SELECTED & CONFIGURED AS DEFAULT
```

- **Giọng đọc mặc định được chọn:** `Voice C (Onyx HD)`
- **Nhà cung cấp (Provider):** `Beeknoee AI Voice Engine`
- **Model:** `openai/tts-1-hd`
- **Voice ID:** `onyx`
- **Tốc độ đọc (Speed):** `1.00x`
- **Phong cách:** Nam Gym Reviewer, trầm ấm, tự tin, uy lực thể thao, phát âm tiếng Việt chuẩn xác và tự nhiên.
- **Trạng thái lưu trữ:** Đã lưu vào `table_setting` và `libraries/config.php` làm giọng đọc mặc định cho toàn bộ các dự án AI Video của FITNADO.
