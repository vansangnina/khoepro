<style>
    .menu_left_qlnhom{
        padding-top: 50px;
        height: 100vh;
        overflow-y: auto;
        width: 35%;
    }
</style>
<div class="m_mobile_header align-items-center">
	<a   class="menu_l_qlnhom1"><i class="fa fa-bars" aria-hidden="true"></i></a>
</div>

<div class="menu_left_qlnhom scroll_menu">
    <div class="close_menu_left">
        <i class="fa fa-times"></i>
    </div>
    <?= $func->decodeHtmlChars($static['content' . $lang]) ?>
</div>
<div class="menu_overlay"></div>
<?php if (!empty($static)) { ?>
    <div class="title-main"><span><?= $static['name' . $lang] ?></span></div>
    <div class="content-main w-clear"><?= $func->decodeHtmlChars($static['content' . $lang]) ?></div>
    <div class="share">
        <b><?= chiase ?>:</b>
        <div class="social-plugin w-clear">
            <?php
            $params = array();
            $params['oaid'] = $optsetting['oaidzalo'];
            echo $func->markdown('social/share', $params);
            ?>
        </div>
    </div>
<?php } else { ?>
    <div class="alert alert-warning w-100" role="alert">
        <strong><?= dangcapnhatdulieu ?></strong>
    </div>
<?php } ?>

