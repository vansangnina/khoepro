<?php 
	include "config.php";
	$id = (isset($_POST['id']) && $_POST['id'] > 0) ? htmlspecialchars($_POST['id']) : 0;
?>
<div class="paging-product-category paging-product-category-<?=$id?>" data-list="<?=$id?>"></div>
<script type="text/javascript">
    $(document).ready(function() {
        $(".paging-product-category").each(function(){
            var list = $(this).data("list");
            loadPaging("api/product.php?perpage=8&idList="+list,'.paging-product-category-'+list);
        })
    });
</script>