<!-- Js Config -->
<script type="text/javascript">
    var NN_FRAMEWORK = NN_FRAMEWORK || {};
    var CONFIG_BASE = '<?= $configBase ?>';
    var ASSET = '<?= ASSET ?>';
    var WEBSITE_NAME = '<?= (!empty($setting['name' . $lang])) ? addslashes($setting['name' . $lang]) : '' ?>';
    var TIMENOW = '<?= date("d/m/Y", time()) ?>';
    var SHIP_CART = <?= (!empty($config['order']['ship'])) ? 'true' : 'false' ?>;
    var RECAPTCHA_ACTIVE = <?= (!empty($config['googleAPI']['recaptcha']['active'])) ? 'true' : 'false' ?>;
    var RECAPTCHA_SITEKEY = '<?= $config['googleAPI']['recaptcha']['sitekey'] ?>';
    var GOTOP = ASSET + 'assets/images/top.png';
    var LANG = {
        'no_keywords': '<?= chuanhaptukhoatimkiem ?>',
        'delete_product_from_cart': '<?= banmuonxoasanphamnay ?>',
        'no_products_in_cart': '<?= khongtontaisanphamtronggiohang ?>',
        'ward': '<?= phuongxa ?>',
        'back_to_home': '<?= vetrangchu ?>',
        'thongbao': '<?= thongbao ?>',
        'dongy': '<?= dongy ?>',
        'dungluonghinhanhlon': '<?= dungluonghinhanhlon ?>',
        'dulieukhonghople': '<?= dulieukhonghople ?>',
        'banchiduocchon1hinhanhdeuplen': '<?= banchiduocchon1hinhanhdeuplen ?>',
        'dinhdanghinhanhkhonghople': '<?= dinhdanghinhanhkhonghople ?>',
        'huy': '<?= huy ?>',
    };
    var logo_img='<?=$configBase?><?= UPLOAD_PHOTO_L . $logo['photo'] ?>';
</script>

<!-- Js Files -->
<?php
$js->set("js/jquery.min.js");
$js->set("js/lazyload.min.js");
$js->set("bootstrap/bootstrap.js");
$js->set("js/wow.min.js");
$js->set("owlcarousel2/owl.carousel.js");
$js->set("holdon/HoldOn.js");
$js->set("confirm/confirm.js");
$js->set("simplenotify/simple-notify.js");


$js->set("easyticker/easy-ticker.js");
$js->set("fotorama/fotorama.js");
$js->set("photobox/photobox.js");
$js->set("fileuploader/jquery.fileuploader.min.js");
$js->set("datetimepicker/php-date-formatter.min.js");
$js->set("datetimepicker/jquery.mousewheel.js");
$js->set("datetimepicker/jquery.datetimepicker.js");
$js->set("js/comment.js");
$js->set("ckeditor/ckeditor.js");

$js->set("fancybox5/fancybox.umd.js");
$js->set("slick/slick.js");
$js->set("magiczoomplus/magiczoomplus.js");
$js->set("js/functions.js");
$js->set("mmenu/mmenu.js");
$js->set("aos/aos.js");
$js->set("toc/toc.js");
$js->set("js/apps.js");
echo $js->get();
?>
<?php if (!empty($config['googleAPI']['recaptcha']['active'])) { ?>
    <!-- Js Google Recaptcha V3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?= $config['googleAPI']['recaptcha']['sitekey'] ?>">
    </script>
    <script type="text/javascript">
        grecaptcha.ready(function() {
            /* Newsletter */
            generateCaptcha('Newsletter', 'recaptchaResponseNewsletter');

            <?php if ($source == 'contact') { ?>
                /* Contact */
                generateCaptcha('contact', 'recaptchaResponseContact');
            <?php } ?>
        });
    </script>
<?php } ?>

<?php if (!empty($config['oneSignal']['active'])) { ?>
    <!-- Js OneSignal -->
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script type="text/javascript">
        var OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "<?= $config['oneSignal']['id'] ?>"
            });
        });
    </script>
<?php } ?>
<script>
	$(document).ready(function() {
		$('.sua_tinghim').click(function () {
		var id = $(this).data('id');
		var ten = $(this).data('ten');
		$('#tentin').val(ten);
		$('#id_tin').val(id);

		// Gọi AJAX để lấy nội dung chi tiết
		$.ajax({
			url: 'api/get-tin.php',
			type: 'GET',
			data: { id: id },
			success: function (data) {
				// Nếu dùng CKEditor 4
				if (CKEDITOR.instances['form-control-ckeditor-chitiet']) {
					CKEDITOR.instances['form-control-ckeditor-chitiet'].setData(data);
				}
			},
			error: function () {
				alert('Không lấy được nội dung tin!');
			}
		});
	});
	});
</script>
<script type="text/javascript">
	$(document).ready(function(e) {

		

		$('.dropdown').click(function () {
			// $(this).attr('tabindex', 1).focus();
			// $(this).toggleClass('active');
			$(this).find('.dropdownmenu').slideToggle(300);
		});
		$('.dropdown').focusout(function () {
			$(this).removeClass('active');
			$(this).find('.dropdownmenu').slideUp(300);
		});



        $('.chatface').click(function(){
			$('.tinnhan_qlnhom').animate({bottom:0},500);
			$.ajax({
				url:'ajax/tao_session.php',
				success:function(kq){
					console.log(kq);
				}
			});
		});
    });
	$(document).click(function(event) {
		if (!$(event.target).closest('.tinnhan_qlnhom, .chatface').length) {
			$('.tinnhan_qlnhom').animate({bottom: '-100%'}, 500);
		}
	});
</script>

<script>
	
	var getLimit = 6;
	var alias = '2hand-giay-converse-chuck-taylor-1970-black-low-top-162058c-size-37-5';
	
	function activeTab(obj){
		$('.product-tab ul li').removeClass('active');
		$(obj).addClass('active');
		var id = $(obj).attr('data-tab');
		$('.tab-content').removeClass('active');
		$(id).addClass('active');
	}


	$('.product-tab ul li').click(function(){
		activeTab(this);
		return false;
	});
	if (typeof Swiper !== 'undefined') {
		var galleryThumbs = new Swiper('.gallery-thumbs', {
			spaceBetween: 5,
			slidesPerView: 10,
			freeMode: true,
			lazy: true,
			watchSlidesVisibility: true,
			watchSlidesProgress: true,
			hashNavigation: true,
			slideToClickedSlide: true,
			breakpoints: {
				260: {
					slidesPerView: 3,
					spaceBetween: 10,
				},
				300: {
					slidesPerView: 4,
					spaceBetween: 10,
				},
				500: {
					slidesPerView: 4,
					spaceBetween: 10,
				},
				640: {
					slidesPerView: 4,
					spaceBetween: 10,
				},
				768: {
					slidesPerView: 4,
					spaceBetween: 10,
				},
				1024: {
					slidesPerView: 4,
					spaceBetween: 10,
				},
				1199: {
					slidesPerView: 5,
					spaceBetween: 10,
				},
			},
			navigation: {
				nextEl: '.gallery-thumbs .swiper-button-next',
				prevEl: '.gallery-thumbs .swiper-button-prev',
			},
		});
		var galleryTop = new Swiper('.gallery-top', {
			spaceBetween: 0,
			effect: 'fade',
			lazy: true,
			hashNavigation: true,
			thumbs: {
				swiper: galleryThumbs
			}
		});
		var swiper = new Swiper('.product-relate-swiper', {
			slidesPerView: 4,
			loop: false,
			grabCursor: true,
			spaceBetween: 30,
			roundLengths: true,
			slideToClickedSlide: false,
			navigation: {
				nextEl: '.product-relate-swiper .swiper-button-next',
				prevEl: '.product-relate-swiper .swiper-button-prev',
			},
			autoplay: false,
			breakpoints: {
				260: {
					slidesPerView: 'auto',
					spaceBetween: 15
				},
				500: {
					slidesPerView: 2,
					spaceBetween: 15
				},
				640: {
					slidesPerView: 3,
					spaceBetween: 15
				},
				768: {
					slidesPerView: 3,
					spaceBetween: 30
				},
				991: {
					slidesPerView: 4,
					spaceBetween: 30
				},
				1200: {
					slidesPerView: 4,
					spaceBetween: 30
				}
			}
		});
	}
	$(document).ready(function() {
		$("#lightgallery").lightGallery({
			thumbnail: false
		}); 
		$("#videolary").lightGallery({
			thumbnail: false
		}); 
	});
	$('.btn-buyNow').on('click', function(event) {
		$.ajax({
			url:'/cart/add.js',
			type: "post",
			data: $("#add-to-cart-form").serialize(),
			datatype: "json",
			success: function(data){
				window.location.href = "/checkout";
			},
			error: function(){
			}
		});
		event.preventDefault();
	});
	$('.btn--view-more').on('click', function(e){
		e.preventDefault();
		var $this = $(this);
		$this.parents('.product-review-details').find('.product-review-content').toggleClass('expanded');
		$('html, body').animate({ scrollTop: $('.product-review-details').offset().top - 110 }, 'slow');
		$(this).toggleClass('active');
		return false;
	});
	$(document).ready(function ($){

		
		var product = {"id":35084293,"name":"2HAND Giày Converse Chuck Taylor 1970 Black Low Top 162058C SIZE 37.5 PVN12443","alias":"2hand-giay-converse-chuck-taylor-1970-black-low-top-162058c-size-37-5","vendor":"CONVERSE","type":"ĐẾ BẰNG","content":"<p>-Tên sản phẩm:&nbsp;Converse Chuck Taylor 1970 Black Low Top<br />\n-Code:&nbsp;162058C<br />\n- SIZE 37.5<br />\n-MUA Ở ĐÂY :&nbsp;<br />\n&nbsp;<br />\nGIÀY CŨ SÀI GÒN</p>\n<p>Bảo hành chính hãng trọn đời: Tận hưởng sự yên tâm với cam kết bảo hành trọn đời cho sản phẩm này.</p>\n<p>Bảo hành keo 30 ngày: Bạn được bảo hành keo trong vòng 30 ngày kể từ khi nhận hàng để đảm bảo chất lượng sản phẩm.</p>\n<p>Hàng chính hãng 100%: Chúng tôi cam kết sản phẩm hoàn toàn chính hãng. Nếu phát hiện hàng giả, chúng tôi sẽ hoàn trả tiền và tặng thêm một đôi giày.</p>\n<p>Ghi chú: VUI LÒNG LIÊN HỆ VỚI SHOP TRƯỚC KHI ĐẶT HÀNG<br />\n#2hand #giaycusaigon #real</p>","summary":"<p>-Tên sản phẩm:&nbsp;Converse Chuck Taylor 1970 Black Low Top<br />\n-Code:&nbsp;162058C<br />\n- SIZE 37.5</p>","template_layout":"product","available":true,"tags":[],"price":700000.0000,"price_min":700000.0000,"price_max":700000.0000,"price_varies":false,"compare_at_price":0,"compare_at_price_min":0,"compare_at_price_max":0,"compare_at_price_varies":false,"variants":[{"id":112627209,"barcode":null,"sku":"PVN12443","title":"Default Title","options":["Default Title"],"option1":"Default Title","option2":null,"option3":null,"available":true,"taxable":false,"price":700000.0000,"compare_at_price":null,"inventory_management":"bizweb","inventory_policy":"deny","inventory_quantity":1,"weight_unit":"g","weight":0,"requires_shipping":true,"image":{"src":"https://bizweb.dktcdn.net/100/500/384/products/432995764-915496620583808-312573304611190523-n.jpg?v=1711250136083"}}],"featured_image":{"src":"https://bizweb.dktcdn.net/100/500/384/products/432995764-915496620583808-312573304611190523-n.jpg?v=1711250136083"},"images":[{"src":"https://bizweb.dktcdn.net/100/500/384/products/432995764-915496620583808-312573304611190523-n.jpg?v=1711250136083"},{"src":"https://bizweb.dktcdn.net/100/500/384/products/z5273456414182-19e27c74a0c1dbd42b591af00185bcee.jpg?v=1711250136083"},{"src":"https://bizweb.dktcdn.net/100/500/384/products/z5273456428321-d76695fddd179bdd83e3e37f3c909fdb.jpg?v=1711250136083"},{"src":"https://bizweb.dktcdn.net/100/500/384/products/z5273456181113-e810c6d233b54fc1096b9db269951355.jpg?v=1711250136083"},{"src":"https://bizweb.dktcdn.net/100/500/384/products/z5273456209153-4452726f18cdfb770142dcc99c9b64cf.jpg?v=1711250136083"},{"src":"https://bizweb.dktcdn.net/100/500/384/products/z5273456422492-8cb4e507dd38f148a021274827f2b565.jpg?v=1711250136083"},{"src":"https://bizweb.dktcdn.net/100/500/384/products/z5273456270017-b1758bb217e683ced46563baf1f30615.jpg?v=1711250132460"}],"options":["Title"],"created_on":"2024-03-20T21:31:30","modified_on":"2024-03-24T10:15:38","published_on":"2024-03-24T10:13:00"};
		var alias_pro = '2hand-giay-converse-chuck-taylor-1970-black-low-top-162058c-size-37-5';
		var array_list = [product];
		var list_viewed_pro_old = localStorage.getItem('last_viewed_products');
		var last_viewed_pro_new = "";
		if(list_viewed_pro_old == null || list_viewed_pro_old == '')
			last_viewed_pro_new = array_list;
		else{
			var list_viewed_pro_old = JSON.parse(localStorage.last_viewed_products);
			list_viewed_pro_old.splice(10, 1);
			for (i = 0; i < list_viewed_pro_old.length; i++) {
				if ( list_viewed_pro_old[i].alias == alias_pro ) {
					list_viewed_pro_old.splice(i,1);
					break;
				}
			}
			list_viewed_pro_old.unshift(array_list[0]);
			last_viewed_pro_new = list_viewed_pro_old;
		}
		localStorage.setItem('last_viewed_products',JSON.stringify(last_viewed_pro_new));
	});
</script>
<script> 

	
        // window.onpopstate = function(event) { 
        //    alert("location: " + document.location + 
        //    ", state: " + JSON.stringify(event.state)); 
        // }; 
        // history.pushState({ 
        //     page: 1 
        // }, "title 1", "?page=1"); 
        // history.pushState({ 
        //     page: 2 
        // }, "title 2", "?page=2"); 
        // history.replaceState({ 
        //     page: 3 
        // }, "title 3", "?page=3"); 
  
        // alerts "location:  
        // https://ide.geeksforgeeks.org/tryit.php?page=1, 
        // state: {"page":1}" 
        //history.back(); 
  
        // alerts "location: about:blank, state: null" 
        //history.back(); 
    </script> 

<script type="text/javascript" src="assets/js/snap.svg-min.js"></script>
<script type="text/javascript" src="assets/js/enlivenem.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
		// Mở menu
		$('.menu_l_qlnhom1').on('click', function(e) {
			e.stopPropagation();
			$('.menu_left_qlnhom').addClass('active');
		});

		// Đóng menu khi bấm nút X
		$('.close_menu_left').on('click', function() {
			$('.menu_left_qlnhom').removeClass('active');
		});

		// Click ra ngoài để đóng menu
		$(document).on('click', function(e) {
			if (!$('.menu_left_qlnhom').is(e.target) && $('.menu_left_qlnhom').has(e.target).length === 0 &&
				!$('.menu_l_qlnhom1').is(e.target) && $('.menu_l_qlnhom1').has(e.target).length === 0) {
				$('.menu_left_qlnhom').removeClass('active');
			}
		});

        setTimeout(function () {
            $("#loading").addClass("show");
        }, 900);
        setTimeout(function () {
            $("#loading").addClass("finish");
        }, 3900);
        setTimeout(function () {
            $("#loading").remove();
        }, 4300);

		//document.addEventListener('scroll', onScroll);
		$('a[href^="#"]').on('click', function(event){
			var target = $(this.getAttribute('href'));
			if(target.length) {
				event.preventDefault();
				$('html, body').animate({
					scrollTop: target.offset().top - 100
				}, 200);
			}
		});
		$(window).scroll(function () {
			var scrollDistance = $(window).scrollTop();
			$('.section_hosting').each(function (i) {
				var paddT = parseInt($(this).css("padding-top"));
				var minus = 100 - paddT ;
				if ($(this).offset().top - minus <= scrollDistance) {
					var target = $(this).attr("id");
					$('.scroll_tabnav_version3.tab-nav a[href*="#"]:not([href="#"]).active').removeClass('active');
					$('.scroll_tabnav_version3.tab-nav a[href*="#'+target+'"]').addClass('active');
				}
			});

		}).scroll();

    });
</script>

 <script>
	// Lấy múi giờ trình duyệt
	const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

	// Tạo input ẩn để gửi múi giờ
	const tzInput = document.createElement('input');
	tzInput.type = 'hidden';
	tzInput.name = 'user_timezone';
	tzInput.value = timezone;
	var orderFormEl = document.getElementById('orderForm');
	if (orderFormEl) {
		orderFormEl.appendChild(tzInput);
	}
</script>

<!-- Js Structdata -->
<?php include TEMPLATE . LAYOUT . "strucdata.php"; ?>

<!-- Js Addons -->
<?= $addons->set('script-main', 'script-main', 2); ?>
<?= $addons->get(); ?>

<!-- Js Body -->
<?= $func->decodeHtmlChars($setting['bodyjs']) ?>
