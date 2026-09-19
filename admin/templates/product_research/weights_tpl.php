<?php
$linkSaveWeights = "index.php?com=product_research&act=save_weights";
$linkMan = "index.php?com=product_research&act=man";
?>
<!-- Content Header -->
<section class="content-header text-sm">
    <div class="container-fluid">
        <div class="row">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="index.php" title="<?=dashboard?>"><?=dashboard?></a></li>
                <li class="breadcrumb-item"><a href="<?= $linkMan ?>">Nghiên cứu sản phẩm</a></li>
                <li class="breadcrumb-item active">Cấu hình Trọng số Chấm điểm (Scoring Weights)</li>
            </ol>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="post" action="<?= $linkSaveWeights ?>" onsubmit="return validateWeights()">
                <div class="card card-primary card-outline shadow-sm text-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="fas fa-sliders-h mr-1"></i> Cấu hình Trọng số Động cơ Chấm điểm (Scoring Engine)</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info p-3 text-xs mb-4">
                            <h6><i class="fas fa-info-circle mr-1"></i> <strong>Quy tắc phân bổ trọng số:</strong></h6>
                            Tổng trọng số của cả 5 chiều đánh giá <strong>BẮT BUỘC PHẢI BẰNG 100%</strong>. Điểm số tổng hợp (0 - 100) sẽ phản ánh mức độ ưu tiên nghiên cứu của sản phẩm trên FITNADO.
                        </div>

                        <!-- Demand -->
                        <div class="form-group row align-items-center">
                            <label for="weight_demand" class="col-sm-5 col-form-label font-weight-bold">
                                <i class="fas fa-shopping-cart text-primary mr-1"></i> 1. Nhu cầu thị trường (Demand):
                                <div class="text-xs font-weight-normal text-muted">Lượt bán, Đánh giá sao, Số lượng review, GMV</div>
                            </label>
                            <div class="col-sm-5">
                                <input type="range" class="custom-range" min="0" max="60" step="5" id="slider_demand" value="<?= $activeWeights['demand'] ?? 30 ?>" oninput="syncWeight('demand', this.value)">
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group input-group-sm">
                                    <input type="number" min="0" max="100" class="form-control font-weight-bold text-center weight-input" name="demand" id="weight_demand" value="<?= $activeWeights['demand'] ?? 30 ?>" oninput="syncSlider('demand', this.value)">
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Content Potential -->
                        <div class="form-group row align-items-center">
                            <label for="weight_content" class="col-sm-5 col-form-label font-weight-bold">
                                <i class="fas fa-video text-info mr-1"></i> 2. Tiềm năng nội dung (Content Potential):
                                <div class="text-xs font-weight-normal text-muted">Lượt xem video, Số creator, Tính trực quan & Pain point</div>
                            </label>
                            <div class="col-sm-5">
                                <input type="range" class="custom-range" min="0" max="60" step="5" id="slider_content" value="<?= $activeWeights['content'] ?? 25 ?>" oninput="syncWeight('content', this.value)">
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group input-group-sm">
                                    <input type="number" min="0" max="100" class="form-control font-weight-bold text-center weight-input" name="content" id="weight_content" value="<?= $activeWeights['content'] ?? 25 ?>" oninput="syncSlider('content', this.value)">
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Commission -->
                        <div class="form-group row align-items-center">
                            <label for="weight_commission" class="col-sm-5 col-form-label font-weight-bold">
                                <i class="fas fa-percentage text-success mr-1"></i> 3. Tiềm năng hoa hồng (Commission):
                                <div class="text-xs font-weight-normal text-muted">Tỷ lệ hoa hồng %, Giá trị hoa hồng trên mỗi đơn hàng</div>
                            </label>
                            <div class="col-sm-5">
                                <input type="range" class="custom-range" min="0" max="60" step="5" id="slider_commission" value="<?= $activeWeights['commission'] ?? 20 ?>" oninput="syncWeight('commission', this.value)">
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group input-group-sm">
                                    <input type="number" min="0" max="100" class="form-control font-weight-bold text-center weight-input" name="commission" id="weight_commission" value="<?= $activeWeights['commission'] ?? 20 ?>" oninput="syncSlider('commission', this.value)">
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Competition -->
                        <div class="form-group row align-items-center">
                            <label for="weight_competition" class="col-sm-5 col-form-label font-weight-bold">
                                <i class="fas fa-chess text-warning mr-1"></i> 4. Cơ hội cạnh tranh (Competition):
                                <div class="text-xs font-weight-normal text-muted">Độ bão hòa thị trường (Điểm cao = Ít đối thủ thống trị)</div>
                            </label>
                            <div class="col-sm-5">
                                <input type="range" class="custom-range" min="0" max="60" step="5" id="slider_competition" value="<?= $activeWeights['competition'] ?? 15 ?>" oninput="syncWeight('competition', this.value)">
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group input-group-sm">
                                    <input type="number" min="0" max="100" class="form-control font-weight-bold text-center weight-input" name="competition" id="weight_competition" value="<?= $activeWeights['competition'] ?? 15 ?>" oninput="syncSlider('competition', this.value)">
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- SEO -->
                        <div class="form-group row align-items-center">
                            <label for="weight_seo" class="col-sm-5 col-form-label font-weight-bold">
                                <i class="fas fa-search-dollar text-secondary mr-1"></i> 5. Tiềm năng SEO (SEO Opportunity):
                                <div class="text-xs font-weight-normal text-muted">Từ khóa chính, Search Intent dài hạn</div>
                            </label>
                            <div class="col-sm-5">
                                <input type="range" class="custom-range" min="0" max="60" step="5" id="slider_seo" value="<?= $activeWeights['seo'] ?? 10 ?>" oninput="syncWeight('seo', this.value)">
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group input-group-sm">
                                    <input type="number" min="0" max="100" class="form-control font-weight-bold text-center weight-input" name="seo" id="weight_seo" value="<?= $activeWeights['seo'] ?? 10 ?>" oninput="syncSlider('seo', this.value)">
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Total Calculation Indicator -->
                        <div class="d-flex justify-content-between align-items-center p-3 rounded bg-light border">
                            <span class="font-weight-bold text-uppercase">Tổng trọng số hiện tại:</span>
                            <div class="d-flex align-items-center">
                                <span id="total_weight_display" class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 1.1rem;">100%</span>
                            </div>
                        </div>
                        <div id="weight_error_msg" class="text-danger text-xs mt-2 d-none">
                            <i class="fas fa-exclamation-circle mr-1"></i> Tổng trọng số phải bằng đúng 100% để lưu cấu hình.
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetDefaultWeights()">
                            <i class="fas fa-undo mr-1"></i> Khôi phục mặc định (30/25/20/15/10)
                        </button>
                        <div style="gap:10px;" class="d-flex">
                            <a href="<?= $linkMan ?>" class="btn btn-secondary btn-sm">Hủy bỏ</a>
                            <button type="submit" id="btn_save_weights" class="btn btn-primary btn-sm font-weight-bold px-4">
                                <i class="fas fa-save mr-1"></i> Lưu cấu hình trọng số
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
function syncWeight(key, val) {
    document.getElementById('weight_' + key).value = val;
    calculateSum();
}

function syncSlider(key, val) {
    document.getElementById('slider_' + key).value = val;
    calculateSum();
}

function calculateSum() {
    var demand = parseFloat(document.getElementById('weight_demand').value) || 0;
    var content = parseFloat(document.getElementById('weight_content').value) || 0;
    var commission = parseFloat(document.getElementById('weight_commission').value) || 0;
    var competition = parseFloat(document.getElementById('weight_competition').value) || 0;
    var seo = parseFloat(document.getElementById('weight_seo').value) || 0;

    var sum = demand + content + commission + competition + seo;
    var display = document.getElementById('total_weight_display');
    var btn = document.getElementById('btn_save_weights');
    var msg = document.getElementById('weight_error_msg');

    display.innerText = sum + '%';

    if (Math.abs(sum - 100) < 0.01) {
        display.className = 'badge badge-success px-3 py-2 font-weight-bold';
        btn.disabled = false;
        msg.classList.add('d-none');
    } else {
        display.className = 'badge badge-danger px-3 py-2 font-weight-bold';
        btn.disabled = true;
        msg.classList.remove('d-none');
    }
}

function resetDefaultWeights() {
    syncWeight('demand', 30);
    syncSlider('demand', 30);
    syncWeight('content', 25);
    syncSlider('content', 25);
    syncWeight('commission', 20);
    syncSlider('commission', 20);
    syncWeight('competition', 15);
    syncSlider('competition', 15);
    syncWeight('seo', 10);
    syncSlider('seo', 10);
}

function validateWeights() {
    var demand = parseFloat(document.getElementById('weight_demand').value) || 0;
    var content = parseFloat(document.getElementById('weight_content').value) || 0;
    var commission = parseFloat(document.getElementById('weight_commission').value) || 0;
    var competition = parseFloat(document.getElementById('weight_competition').value) || 0;
    var seo = parseFloat(document.getElementById('weight_seo').value) || 0;

    var sum = demand + content + commission + competition + seo;
    if (Math.abs(sum - 100) > 0.01) {
        alert('Tổng trọng số phải bằng đúng 100%! Hiện tại: ' + sum + '%');
        return false;
    }
    return true;
}

document.addEventListener('DOMContentLoaded', calculateSum);
</script>
