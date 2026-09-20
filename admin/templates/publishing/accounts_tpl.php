<?php
$linkMan = "index.php?com=publishing&act=man";
$linkAccounts = "index.php?com=publishing&act=accounts";
$linkSaveAccount = "index.php?com=publishing&act=save_account";
?>

<div class="content-header text-sm">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.25rem;">
                    <i class="fas fa-users-cog mr-2 text-purple"></i>Quản lý Tài khoản Xuất bản (Multi-Channel Accounts)
                </h1>
            </div>
            <div class="col-sm-6 text-sm-right">
                <button type="button" class="btn btn-sm btn-success mr-2 shadow-sm font-weight-bold" data-toggle="modal" data-target="#accountModal">
                    <i class="fas fa-plus-circle mr-1"></i> Thêm Tài khoản Mới
                </button>
                <a href="<?=$linkMan?>" class="btn btn-sm btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Danh sách bài đăng
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content text-sm pb-4">
    <div class="container-fluid">
        <div class="card card-outline card-purple shadow-sm">
            <div class="card-header p-2">
                <span class="font-weight-bold text-dark"><i class="fas fa-id-badge mr-1"></i> Danh sách Tài khoản Kênh (TikTok, Facebook, Instagram...)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 60px;" class="text-center">ID</th>
                                <th style="width: 140px;" class="text-center">Nền tảng</th>
                                <th>Tên hiển thị & Handle</th>
                                <th style="width: 150px;" class="text-center">Phương thức</th>
                                <th style="width: 160px;" class="text-center">Trạng thái Ủy quyền</th>
                                <th style="width: 120px;" class="text-center">Mặc định</th>
                                <th style="width: 140px;" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($accounts)): ?>
                                <?php foreach ($accounts as $acc): ?>
                                    <?php $platInfo = PublishingCenter::PLATFORMS[$acc['platform']] ?? array('name' => $acc['platform'], 'badge_class' => 'badge-secondary', 'icon' => 'fas fa-share'); ?>
                                    <tr>
                                        <td class="text-center font-weight-bold">#<?=$acc['id']?></td>
                                        <td class="text-center">
                                            <span class="badge <?=$platInfo['badge_class']?> p-2">
                                                <i class="<?=$platInfo['icon']?> mr-1"></i> <?=$platInfo['name']?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark" style="font-size: 1rem;"><?=htmlspecialchars($acc['account_name'])?></div>
                                            <div class="text-primary font-weight-bold"><code><?=htmlspecialchars($acc['account_handle'])?></code></div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($acc['provider'] === 'manual'): ?>
                                                <span class="badge badge-info"><i class="fas fa-user-edit mr-1"></i> Thủ công (Manual)</span>
                                            <?php else: ?>
                                                <span class="badge badge-purple"><i class="fas fa-robot mr-1"></i> API Direct</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($acc['auth_status'] === 'AUTHORIZED'): ?>
                                                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> ĐÃ ỦY QUYỀN</span>
                                            <?php elseif ($acc['auth_status'] === 'EXPIRED'): ?>
                                                <span class="badge badge-warning"><i class="fas fa-exclamation-triangle mr-1"></i> HẾT HẠN</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary"><i class="fas fa-shield-alt mr-1"></i> MANUAL ONLY</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($acc['is_default'])): ?>
                                                <span class="badge badge-success"><i class="fas fa-star mr-1"></i> Mặc định</span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick='editAccount(<?=json_encode($acc)?>)'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="index.php?com=publishing&act=delete_account&id=<?=$acc['id']?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa tài khoản này?')" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Chưa có tài khoản xuất bản nào.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Thêm/Sửa Tài Khoản -->
<div class="modal fade" id="accountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="<?=$linkSaveAccount?>" method="POST" class="modal-content">
            <input type="hidden" name="id" id="modal_acc_id" value="0">
            <div class="modal-header bg-purple text-white p-3">
                <h5 class="modal-title font-weight-bold" id="modal_title"><i class="fas fa-user-plus mr-1"></i> Thêm Tài Khoản Kênh Mới</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="form-group mb-3">
                    <label class="font-weight-bold text-dark">Nền tảng: <span class="text-danger">*</span></label>
                    <select name="platform" id="modal_platform" class="form-control" required>
                        <?php foreach (PublishingCenter::PLATFORMS as $pKey => $pInfo): ?>
                            <option value="<?=$pKey?>"><?=$pInfo['name']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="font-weight-bold text-dark">Tên Kênh Hiển thị: <span class="text-danger">*</span></label>
                    <input type="text" name="account_name" id="modal_account_name" class="form-control" placeholder="Ví dụ: FITNADO Official TikTok" required>
                </div>
                <div class="form-group mb-3">
                    <label class="font-weight-bold text-dark">Handle / Username: <span class="text-danger">*</span></label>
                    <input type="text" name="account_handle" id="modal_account_handle" class="form-control" placeholder="Ví dụ: @fitnado.vn" required>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold text-dark">Phương thức:</label>
                        <select name="provider" id="modal_provider" class="form-control">
                            <option value="manual">Thủ công (Manual)</option>
                            <option value="tiktok_api">TikTok API</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold text-dark">Đặt làm mặc định:</label>
                        <select name="is_default" id="modal_is_default" class="form-control">
                            <option value="0">Không</option>
                            <option value="1">Có (Default)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-2 bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-sm btn-success font-weight-bold shadow-sm"><i class="fas fa-save mr-1"></i> Lưu Tài Khoản</button>
            </div>
        </form>
    </div>
</div>

<script>
function editAccount(acc) {
    document.getElementById('modal_acc_id').value = acc.id;
    document.getElementById('modal_title').innerHTML = '<i class="fas fa-edit mr-1"></i> Cập Nhật Tài Khoản #' + acc.id;
    document.getElementById('modal_platform').value = acc.platform;
    document.getElementById('modal_account_name').value = acc.account_name;
    document.getElementById('modal_account_handle').value = acc.account_handle;
    document.getElementById('modal_provider').value = acc.provider;
    document.getElementById('modal_is_default').value = acc.is_default;
    $('#accountModal').modal('show');
}
</script>
