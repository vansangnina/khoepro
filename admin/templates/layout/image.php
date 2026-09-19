<div class="photoUpload-zone">
    <div class="photoUpload-detail" id="photoUpload-preview">
        <?php if(!empty($photoDetail['image'])){?>
            <a data-fancybox href="../<?=$photoDetail['upload']?><?=$photoDetail['image']?>"><?= $func->getImage(['class' => 'rounded', 'size-error' => '250x250x1', 'upload' => $photoDetail['upload'], 'image' => $photoDetail['image'], 'alt' => 'Alt Photo']) ?></a>
            <div class="delete-photo">
                <a href="javascript:void(0)" title="Xóa hình ảnh" data-action="<?=$photoAction?>" data-table="<?=$com?>" data-upload="<?=($com=='product')?'product':(($com=='photo')?'photo':'news')?>" data-id="<?=$item['id']?>"><i class="far fa-trash-alt"></i></a>
            </div>
        <?php }else{?>
            <?=$func->getImage(['class' => 'rounded', 'size-error' => '250x250x1', 'upload' => $photoDetail['upload'], 'image' => $photoDetail['image'], 'alt' => 'Alt Photo']) ?>
        <?php }?>
    </div>
    <label class="photoUpload-file" id="photo-zone" for="file-zone">
        <input type="file" name="file" id="file-zone">
        <i class="fas fa-cloud-upload-alt"></i>
        <p class="photoUpload-drop"><?=keovathahinhvaoday?></p>
        <p class="photoUpload-or"><?=hoac?></p>
        <p class="photoUpload-choose btn btn-sm bg-gradient-success"><?=chonhinh?></p>
    </label>
    <div class="photoUpload-dimension"><?= $photoDetail['dimension'] ?></div>
</div>