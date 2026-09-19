/* Validation form */
validateForm('validation-newsletter');
validateForm('validation-cart');
validateForm('validation-user');
validateForm('validation-contact');

/* Lazys */
NN_FRAMEWORK.Lazys = function () {
	if (isExist($('.lazy'))) {
		var lazyLoadInstance = new LazyLoad({
			elements_selector: '.lazy'
		});
	}
};

/* Load name input file */
NN_FRAMEWORK.loadNameInputFile = function () {
	if (isExist($('.custom-file input[type=file]'))) {
		$('body').on('change', '.custom-file input[type=file]', function () {
			var fileName = $(this).val();
			fileName = fileName.substr(fileName.lastIndexOf('\\') + 1, fileName.length);
			$(this).siblings('label').html(fileName);
		});
	}
};

/* Back to top */
NN_FRAMEWORK.GoTop = function () {
	$(window).scroll(function () {
		if (!$('.scrollToTop').length)
			$('body').append('<div class="scrollToTop"><img src="' + GOTOP + '" alt="Go Top"/></div>');
		if ($(this).scrollTop() > 100) $('.scrollToTop').fadeIn();
		else $('.scrollToTop').fadeOut();
	});

	$('body').on('click', '.scrollToTop', function () {
		$('html, body').animate({ scrollTop: 0 }, 800);
		return false;
	});
};

/* Alt images */
NN_FRAMEWORK.AltImg = function () {
	$('img').each(function (index, element) {
		if (!$(this).attr('alt') || $(this).attr('alt') == '') {
			$(this).attr('alt', WEBSITE_NAME);
		}
	});
};

/* Menu */
NN_FRAMEWORK.Menu = function () {
	/* Menu remove empty ul */
	if (isExist($('.menu'))) {
		$('.menu ul li a').each(function () {
			$this = $(this);

			if (!isExist($this.next('ul').find('li'))) {
				$this.next('ul').remove();
				$this.removeClass('has-child');
			}
		});
	}

	/* Menu fixed */
    $(window).scroll(function() {
        var cach_top = $(window).scrollTop();
        var heaigt_header = $(".heade").height()+$(".w-menu").height();

        if (cach_top >= heaigt_header) {
            if (!$(".w-menu").hasClass("fix_head animate__animated animate__fadeIn")) {
                $(".w-menu").addClass("fix_head animate__animated animate__fadeIn");
            }
        } else {
            $(".w-menu").removeClass("fix_head animate__animated animate__fadeIn");
        }
    });
	/* Mmenu */
	if (isExist($('nav#menu'))) {
		$('nav#menu').mmenu({
			extensions: ['border-full', 'position-left', 'position-front']
		});
	}
};

/* Tools */
NN_FRAMEWORK.Tools = function () {
	if (isExist($('.toolbar'))) {
		$('.footer').css({ marginBottom: $('.toolbar').innerHeight() });
	}
};

/* Popup */
NN_FRAMEWORK.Popup = function () {
	if (isExist($('#popup'))) {
		$('#popup').modal('show');
	}
};

/* Wow */
NN_FRAMEWORK.Wows = function () {
	new WOW().init();
};

/* Pagings */
NN_FRAMEWORK.Pagings = function () {
	/* Products */
	if (isExist($('.paging-product'))) {
		loadPaging('api/product.php?perpage=4', '.paging-product');
	}

	/* Categories */
	if (isExist($('.paging-product-category'))) {
		$('.paging-product-category').each(function () {
			var list = $(this).data('list');
			loadPaging('api/product.php?perpage=4&idList=' + list, '.paging-product-category-' + list);
		});
	}
    if (isExist($('.show_padding'))) {
        $(".show_padding").each(function() {
            var list = $(this).data("list");
            var cat = $(this).data("cat");
            loadPaging("api/product.php?perpage=" + "4" + "&idList=" + list + "&idCat=" + cat, '.show_padding' + list);
        })
    }
	if (isExist($('.choose_list'))) {
        $(".choose_list span").click(function() {
            ($(this).parents('.choose_list').find("span").hasClass('choosed')) ? $(this).parents('.choose_list').find("span").removeClass('choosed'):'';
            $(this).addClass('choosed');
            var list = $(this).attr("data-list");
            var cat = $(this).attr("data-cat");
            $(".show_padding" + list).attr("data-list", list);
            $(".show_padding" + list).attr("data-cat", cat);
            loadPaging("api/product.php?perpage=" + "4" + "&idList=" + list + "&idCat=" + cat, '.show_padding' + list);
            return false;
        })
    }
};

/* Ticker scroll 
NN_FRAMEWORK.TickerScroll = function () {
	if (isExist($('.news-scroll'))) {
		$('.news-scroll')
			.easyTicker({
				direction: 'up',
				easing: 'swing',
				speed: 'slow',
				interval: 3500,
				height: 'auto',
				visible: 3,
				mousePause: true,
				controls: {
					up: '.news-control#up',
					down: '.news-control#down'
					// toggle: '.toggle',
					// stopText: 'Stop'
				},
				callbacks: {
					before: function (ul, li) {
						// $(li).css('color', 'red');
					},
					after: function (ul, li) {}
				}
			})
			.data('easyTicker');
	}
};
*/

/* Photobox */
NN_FRAMEWORK.Photobox = function () {
	if (isExist($('.album-gallery'))) {
		$('.album-gallery').photobox('a', { thumbs: true, loop: false });
	}
};

/* Comment */
NN_FRAMEWORK.Comment = function () {
	if (isExist($('.comment-page'))) {
		$('.comment-page').comments({
			url: 'api/comment.php'
		});
	}
};

/* DatePicker */
NN_FRAMEWORK.DatePicker = function () {
	if (isExist($('#birthday'))) {
		$('#birthday').datetimepicker({
			timepicker: false,
			format: 'd/m/Y',
			formatDate: 'd/m/Y',
			minDate: '01/01/1950',
			maxDate: TIMENOW
		});
	}
};

/* Search */
NN_FRAMEWORK.Search = function () {
	if (isExist($(".icon-search"))) {
        $(".icon-search").click(function() {
            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
                $(".search-grid").stop(true, true).animate({opacity: "0",width: "0px"}, 200);
            } else {
                $(this).addClass("active");
                $(".search-grid").stop(true, true).animate({opacity: "1",width: "230px"}, 200);
            }
            document.getElementById($(this).next().find("input").attr("id")).focus();
            $(".icon-search i").toggleClass("bi bi-x-lg");
        });
    }
};

/* Videos */
NN_FRAMEWORK.Videos = function () {
	Fancybox.bind("[data-fancybox]", {});
};

/* Owl Data */
NN_FRAMEWORK.OwlData = function (obj) {
	if (!isExist(obj)) return false;
	var items = obj.attr('data-items');
	var rewind = Number(obj.attr('data-rewind')) ? true : false;
	var autoplay = Number(obj.attr('data-autoplay')) ? true : false;
	var loop = Number(obj.attr('data-loop')) ? true : false;
	var lazyLoad = Number(obj.attr('data-lazyload')) ? true : false;
	var mouseDrag = Number(obj.attr('data-mousedrag')) ? true : false;
	var touchDrag = Number(obj.attr('data-touchdrag')) ? true : false;
	var animations = obj.attr('data-animations') || false;
	var smartSpeed = Number(obj.attr('data-smartspeed')) || 800;
	var autoplaySpeed = Number(obj.attr('data-autoplayspeed')) || 800;
	var autoplayTimeout = Number(obj.attr('data-autoplaytimeout')) || 5000;
	var dots = Number(obj.attr('data-dots')) ? true : false;
	var responsive = {};
	var responsiveClass = true;
	var responsiveRefreshRate = 200;
	var nav = Number(obj.attr('data-nav')) ? true : false;
	var navContainer = obj.attr('data-navcontainer') || false;
	var navTextTemp =
		"<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-chevron-left' width='44' height='45' viewBox='0 0 24 24' stroke-width='1.5' stroke='#2c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><polyline points='15 6 9 12 15 18' /></svg>|<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-chevron-right' width='44' height='45' viewBox='0 0 24 24' stroke-width='1.5' stroke='#2c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><polyline points='9 6 15 12 9 18' /></svg>";
	var navText = obj.attr('data-navtext');
	navText = nav && navContainer && (((navText === undefined || Number(navText)) && navTextTemp) || (isNaN(Number(navText)) && navText) || (Number(navText) === 0 && false));

	if (items) {
		items = items.split(',');

		if (items.length) {
			var itemsCount = items.length;

			for (var i = 0; i < itemsCount; i++) {
				var options = items[i].split('|'),
					optionsCount = options.length,
					responsiveKey;

				for (var j = 0; j < optionsCount; j++) {
					const attr = options[j].indexOf(':') ? options[j].split(':') : options[j];

					if (attr[0] === 'screen') {
						responsiveKey = Number(attr[1]);
					} else if (Number(responsiveKey) >= 0) {
						responsive[responsiveKey] = {
							...responsive[responsiveKey],
							[attr[0]]: (isNumeric(attr[1]) && Number(attr[1])) ?? attr[1]
						};
					}
				}
			}
		}
	}

	if (nav && navText) {
		navText = navText.indexOf('|') > 0 ? navText.split('|') : navText.split(':');
		navText = [navText[0], navText[1]];
	}

	obj.owlCarousel({
		rewind,
		autoplay,
		loop,
		lazyLoad,
		mouseDrag,
		touchDrag,
		smartSpeed,
		autoplaySpeed,
		autoplayTimeout,
		dots,
		nav,
		navText,
		navContainer: nav && navText && navContainer,
		responsiveClass,
		responsiveRefreshRate,
		responsive
	});

	if (autoplay) {
		obj.on('translate.owl.carousel', function (event) {
			obj.trigger('stop.owl.autoplay');
		});

		obj.on('translated.owl.carousel', function (event) {
			obj.trigger('play.owl.autoplay', [autoplayTimeout]);
		});
	}

	if (animations && isExist(obj.find('[owl-item-animation]'))) {
		var animation_now = '';
		var animation_count = 0;
		var animations_excuted = [];
		var animations_list = animations.indexOf(',') ? animations.split(',') : animations;

		obj.on('changed.owl.carousel', function (event) {
			$(this).find('.owl-item.active').find('[owl-item-animation]').removeClass(animation_now);
		});

		obj.on('translate.owl.carousel', function (event) {
			var item = event.item.index;

			if (Array.isArray(animations_list)) {
				var animation_trim = animations_list[animation_count].trim();

				if (!animations_excuted.includes(animation_trim)) {
					animation_now = 'animate__animated ' + animation_trim;
					animations_excuted.push(animation_trim);
					animation_count++;
				}

				if (animations_excuted.length == animations_list.length) {
					animation_count = 0;
					animations_excuted = [];
				}
			} else {
				animation_now = 'animate__animated ' + animations_list.trim();
			}
			$(this).find('.owl-item').eq(item).find('[owl-item-animation]').addClass(animation_now);
		});
	}
};

/* Owl Page */
NN_FRAMEWORK.OwlPage = function () {
	if (isExist($('.owl-page'))) {
		$('.owl-page').each(function () {
			NN_FRAMEWORK.OwlData($(this));
		});
	}
};

/* Dom Change */
NN_FRAMEWORK.DomChange = function () {
	/* Video Fotorama */
	$('#video-fotorama').one('DOMSubtreeModified', function () {
		$('#fotorama-videos').fotorama();
	});

	/* Video Select */
	$('#video-select').one('DOMSubtreeModified', function () {
		$('.listvideos').change(function () {
			var id = $(this).val();
			$.ajax({
				url: 'api/video.php',
				type: 'POST',
				dataType: 'html',
				data: {
					id: id
				},
				beforeSend: function () {
					holdonOpen();
				},
				success: function (result) {
					$('.video-main').html(result);
					holdonClose();
				}
			});
		});
	});

	/* Chat Facebook */
	$('#messages-facebook').one('DOMSubtreeModified', function () {
		$('.js-facebook-messenger-box').on('click', function () {
			$('.js-facebook-messenger-box, .js-facebook-messenger-container').toggleClass('open'),
				$('.js-facebook-messenger-tooltip').length && $('.js-facebook-messenger-tooltip').toggle();
		}),
			$('.js-facebook-messenger-box').hasClass('cfm') &&
				setTimeout(function () {
					$('.js-facebook-messenger-box').addClass('rubberBand animated');
				}, 3500),
			$('.js-facebook-messenger-tooltip').length &&
				($('.js-facebook-messenger-tooltip').hasClass('fixed')
					? $('.js-facebook-messenger-tooltip').show()
					: $('.js-facebook-messenger-box').on('hover', function () {
							$('.js-facebook-messenger-tooltip').show();
					  }),
				$('.js-facebook-messenger-close-tooltip').on('click', function () {
					$('.js-facebook-messenger-tooltip').addClass('closed');
				}));
		$('.search_open').click(function () {
			$('.search_box_hide').toggleClass('opening');
		});
	});
};

/* Quick View */
NN_FRAMEWORK.QuickView = function(obj) {
    $("body").on("click", ".product-quick-view", function() {
        var slug = $(this).attr("data-slug");

        if (slug) {
            $.ajax({
                type: "POST",
                url: slug + "?quickview=1",
                dataType: "html",
                beforeSend: function() {
                    holdonOpen();
                },
                success: function(result) {
                    holdonClose();
                    $("#popup-quickview").find(".modal-body").html(result);
                    $("#popup-quickview").modal("show");
                    // MagicZoom.refresh("Zoom-quickview");
                    // NN_FRAMEWORK.OwlData($('.owl-pro-detail'));
                    MagicZoom.refresh("Zoom-1");
                    NN_FRAMEWORK.OwlData($(".owl-pro-detail"));
                    NN_FRAMEWORK.Lazys();
                },
            });
        }
    });
};

/* Cart */
NN_FRAMEWORK.Cart = function () {
	/* Add */
	if (isExist($('.select-city-cart'))) {
		fetch(CONFIG_BASE+"assets/jsons/city-group.json",{headers: {"Content-Type": "application/json"}}).then(response => {
			return response.json();
		}).then(function(data) {
			$.each(data.citysCentral, function(index, val) {
				$('.select-city-cart').append(`<option value="`+val.id+`">`+val.name+`</option>`);
			});
		});
	}
	$('body').on('click', '.addcart', function () {
		$this = $(this);
		$parents = $this.parents('.right-pro-detail');
		var id = $this.data('id');
		var action = $this.data('action');
		var quantity = $parents.find('.quantity-pro-detail').find('.qty-pro').val();
		quantity = quantity ? quantity : 1;
		var color = $parents.find('.color-block-pro-detail').find('.color-pro-detail input:checked').val();
		color = color ? color : 0;
		var size = $parents.find('.size-block-pro-detail').find('.size-pro-detail input:checked').val();
		size = size ? size : 0;

		if (id) {
			$.ajax({
				url: 'api/cart.php',
				type: 'POST',
				dataType: 'json',
				async: false,
				data: {
					cmd: 'add-cart',
					id: id,
					color: color,
					size: size,
					quantity: quantity
				},
				beforeSend: function () {
					holdonOpen();
				},
				success: function (result) {
					if (action == 'addnow') {
						$('.count-cart').html(result.max);
						$.ajax({
							url: 'api/cart.php',
							type: 'POST',
							dataType: 'html',
							async: false,
							data: {
								cmd: 'popup-cart'
							},
							success: function (result) {
								$('#popup-cart .modal-body').html(result);
								$('#popup-cart').modal('show');
								NN_FRAMEWORK.Lazys();
								holdonClose();
							}
						});
					} else if (action == 'buynow') {
						window.location = CONFIG_BASE + 'gio-hang';
					}
				}
			});
		}
	});

	/* Delete */
	$('body').on('click', '.del-procart', function () {
		confirmDialog('delete-procart', LANG['delete_product_from_cart'], $(this));
	});

	/* Counter */
	$('body').on('click', '.counter-procart', function () {
		var $button = $(this);
		var quantity = 1;
		var input = $button.parent().find('input');
		var id = input.data('pid');
		var code = input.data('code');
		var oldValue = $button.parent().find('input').val();
		if ($button.text() == '+') quantity = parseFloat(oldValue) + 1;
		else if (oldValue > 1) quantity = parseFloat(oldValue) - 1;
		$button.parent().find('input').val(quantity);
		updateCart(id, code, quantity);
	});

	/* Quantity */
	$('body').on('change', 'input.quantity-procart', function () {
		var quantity = $(this).val() < 1 ? 1 : $(this).val();
		$(this).val(quantity);
		var id = $(this).data('pid');
		var code = $(this).data('code');
		updateCart(id, code, quantity);
	});

	/* City */
	if (isExist($('.select-city-cart'))) {
		$('.select-city-cart').change(function () {
			var id = $(this).val();
			loadDistrict(id);
			loadShip();
		});
	}

	/* District */
	if (isExist($('.select-district-cart'))) {
		$('.select-district-cart').change(function () {
			var id = $(this).val();
			var city = $('.select-city-cart').val();
			loadWard(city,id);
			loadShip();
		});
	}

	/* Ward */
	if (isExist($('.select-ward-cart'))) {
		$('.select-ward-cart').change(function () {
			var id = $(this).val();
			loadShip(id);
		});
	}

	/* Payments */
	if (isExist($('.payments-label'))) {
		$('.payments-label').click(function () {
			var payments = $(this).data('payments');
			$('.payments-cart .payments-label, .payments-info').removeClass('active');
			$(this).addClass('active');
			$('.payments-info-' + payments).addClass('active');
		});
	}

	/* Colors */
	
	//if (isExist($('.color-pro-detail'))) {
		
		$('body').on('click', '.color-pro-detail input', function(event) {
			
			$this = $(this).parents('label.color-pro-detail');
			$parents = $this.parents('.attr-pro-detail');
			$parents_detail = $this.parents('.grid-pro-detail');
			$parents.find('.color-block-pro-detail').find('.color-pro-detail').removeClass('active');
			$parents.find('.color-block-pro-detail').find('.color-pro-detail input').prop('checked', false);
			$this.addClass('active');
			$this.find('input').prop('checked', true);
			var id_color = $parents.find('.color-block-pro-detail').find('.color-pro-detail input:checked').val();
			var id_pro = $this.data('idproduct');

			$.ajax({
				url: 'api/color.php',
				type: 'POST',
				dataType: 'html',
				data: {
					id_color: id_color,
					id_pro: id_pro
				},
				beforeSend: function () {
					holdonOpen();
				},
				success: function (result) {
					if (result) {
						$parents_detail.find('.left-pro-detail').html(result);
						MagicZoom.refresh('Zoom-1');
						NN_FRAMEWORK.OwlData($('.owl-pro-detail'));
						NN_FRAMEWORK.Lazys();
					}
					holdonClose();
				}
			});
		});
	//}

	/* Sizes */
	$('body').on('click', '.size-pro-detail input', function(event) {
		$this = $(this).parent();
		$parents = $this.parents('.attr-pro-detail');
		console.log($(this));
		$parents.find('.size-block-pro-detail').find('.size-pro-detail').removeClass('active');
		$parents.find('.size-block-pro-detail').find('.size-pro-detail input').prop('checked', false);
		$this.addClass('active');
		$this.find('input').prop('checked', true);
	});

	/* Quantity detail page */
	//if (isExist($('.quantity-pro-detail span'))) {
		$('body').on('click', '.quantity-pro-detail span', function(event) {
			var $button = $(this);
			var oldValue = $button.parent().find('input').val();
			if ($button.text() == '+') {
				var newVal = parseFloat(oldValue) + 1;
			} else {
				if (oldValue > 1) var newVal = parseFloat(oldValue) - 1;
				else var newVal = 1;
			}
			$button.parent().find('input').val(newVal);
		});
	//}
};

/* Slick */
NN_FRAMEWORK.SlickPage = function() {
	if (isExist($(".slide-text"))) {
        $(".slide-text").slick({
            dots: true,
            infinite: true,
            autoplaySpeed: 3000,
            slidesToShow: 1,
            slidesToScroll: 1,
            adaptiveHeight: true,
            autoplay: true,
            arrows: true,
            fade: true,
        });
    }
    if (isExist($(".slick-v-3"))) {
        $(".slick-v-3").slick({
            dots: false,
            infinite: true,
            autoplaySpeed: 3000,
            slidesToShow: 3,
            slidesToScroll: 1,
            adaptiveHeight: true,
            vertical: true,
            autoplay: true,
            infinite: true,
            arrows: false,
        });
    }
};

/* Aos */
NN_FRAMEWORK.AosAnimation = function() {
    AOS.init({});
};

/* TOC */
NN_FRAMEWORK.Toc = function(){
    if(isExist($(".toc-list")))
    {
        $(".toc-list").toc({
            content: "div#toc-content",
            headings: "h2,h3,h4"
        });

        if(!$(".toc-list li").length) $(".meta-toc").hide();
        if(!$(".toc-list li").length) $(".meta-toc .mucluc-dropdown-list_button").hide();

        $('.toc-list').find('a').click(function(){
            var x = $(this).attr('data-rel');
            goToByScroll(x);
        });

        $("body").on("click",".mucluc-dropdown-list_button", function () {
            $(".box-readmore").slideToggle(200);
        });

        $(document).scroll(function() {
            var y = $(this).scrollTop();
            if (y > 300) {
                $('.meta-toc').addClass('fiedx');
            } else {
                $('.meta-toc').removeClass('fiedx');
            }
        });
    }
};

NN_FRAMEWORK.LoaderWrapper = function() {
	if (isExist($("#loader-wrapper"))) {
        setTimeout(function() {
            $("#loader-wrapper").addClass('show1');
        }, 1500);
        setTimeout(function() {
            $('#loader-wrapper').remove();
        }, 3000);
    }
};

NN_FRAMEWORK.Homes = function() {
    if (isExist($(".list-hot"))) {
        //FirstLoadAPI(".list-hot a:first", "api/load_ajax_product.php", ".load_ajax_product");
        //LoadAPI(".list-hot a", "api/load_ajax_product.php", ".load_ajax_product");
    }

    if (isExist($(".cats-bar-icon"))) {
        $("body").on("click", ".cats-bar-icon", function() {
            $this = $(this);
            $this.toggleClass("active not-active");
            var isActive = $this.hasClass("active");
            $(".cats-owl").animate({
                opacity: +isActive,
                visibility: isActive ? "visible" : "hidden",
            }, 1000, function() {});
        });
    }
	if (isExist($(".content-text"))) {
		$(".content-text table").each(function(i,val){
			$(this).addClass("table table-bordered");
		});
	}
	
};
NN_FRAMEWORK.aweOwlPage = function() {
	var owl = $('.owl-carousel.in-page');
  	owl.each( function(){
		var xs_item = $(this).attr('data-xs-items');
		var md_item = $(this).attr('data-md-items');
		var lg_item = $(this).attr('data-lg-items');
		var sm_item = $(this).attr('data-sm-items');	
		var margin=$(this).attr('data-margin');
		var dot=$(this).attr('data-dot');
		var nav=$(this).attr('data-nav');
		var height=$(this).attr('data-height');
		var play=$(this).attr('data-play');
		var loop=$(this).attr('data-loop');
		
		if (typeof margin !== typeof undefined && margin !== false) {    
		} else{
			margin = 30;
		}
		if (typeof xs_item !== typeof undefined && xs_item !== false) {    
		} else{
			xs_item = 1;
		}
		if (typeof sm_item !== typeof undefined && sm_item !== false) {    

		} else{
			sm_item = 3;
		}	
		if (typeof md_item !== typeof undefined && md_item !== false) {    
		} else{
			md_item = 3;
		}
		if (typeof lg_item !== typeof undefined && lg_item !== false) {    
		} else{
			lg_item = 3;
		}

		if (loop == 1) { loop = true; } else{ loop = false; }
		if (dot == 1) { dot = true; } else{ dot = false; }
		if (nav == 1) { nav = true; } else{ nav = false; }
		if (play == 1) { play = true; } else{ play = false; }
		
		$(this).owlCarousel({
			loop: loop,
			margin:Number(margin),
			responsiveClass:true,
			dots:dot,
			nav:nav,
			navText: ['<div class="owlleft"><svg viewBox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;"><polyline class="a" points="11040,1920 4960,8000 11040,14080 "></polyline></svg></div>','<div class="owlright"><svg viewBox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;"><polyline class="a" points="4960,1920 11040,8000 4960,14080 "></polyline></svg></div>'],
			autoplay:play,
			autoplayTimeout: 4000,
			smartSpeed: 3000,
			autoplayHoverPause:true,
			autoHeight:false,
			responsive:{
				0:{
					items:Number(xs_item)				
				},
				600:{
					items:Number(sm_item)				
				},
				1000:{
					items:Number(md_item)				
				},
				1200:{
					items:Number(lg_item)				
				}
			}
		})
	});
};

NN_FRAMEWORK.slickPage = function(){
	if (isExist($(".slick.in-page"))) {
		$('.slick.in-page').each(function() {
			var dots = $(this).attr('data-dots');
			var infinite = $(this).attr('data-infinite');
			var speed = $(this).attr('data-speed');
			var vertical = $(this).attr('data-vertical');
			var arrows = $(this).attr('data-arrows');
			var autoplay = $(this).attr('data-autoplay');
			var autoplaySpeed = $(this).attr('data-autoplaySpeed');
			var centerMode =  $(this).attr('data-centerMode');
			var centerPadding =  $(this).attr('data-centerPadding');
			var slidesDefault =  $(this).attr('data-slidesDefault');
			var responsive =  $(this).attr('data-responsive');
			var xs_item = $(this).attr('data-xs-items');
			var md_item = $(this).attr('data-md-items');
			var lg_item = $(this).attr('data-lg-items');
			var sm_item = $(this).attr('data-sm-items');
			var slidesDefault_ar = slidesDefault.split(":");
			var xs_item_ar = xs_item.split(":");
			var sm_item_ar = sm_item.split(":");
			var md_item_ar = md_item.split(":");
			var lg_item_ar = lg_item.split(":");
			var to_show = slidesDefault_ar[0];
			var to_scroll = slidesDefault_ar[1];
			if (responsive == 1) { responsive = true; } else{ responsive = false; }
			if (dots == 1) { dots = true; } else{ dots = false; }
			if (arrows == 1) { arrows = true; } else{ arrows = false; }
			if (infinite == 1) { infinite = true; } else{ infinite = false; }
			if (autoplay == 1) { autoplay = true; } else{ autoplay = false; }
			if (centerMode == 1) { centerMode = true; } else{ centerMode = false; }
			if (vertical == 1) { vertical = true; } else{ vertical = false; }
			if (typeof speed !== typeof undefined && speed !== false) {    
			} else{ speed = 300; }
			if (typeof autoplaySpeed !== typeof undefined && autoplaySpeed !== false) {    
			} else{ autoplaySpeed = 2000; }
			if (typeof centerPadding !== typeof undefined && centerPadding !== false) {    
			} else{ centerPadding = "0px"; }
			var reponsive_json = [{
			      	breakpoint: 1024,
			      	settings: {
			        	slidesToShow: Number(lg_item_ar[0]),
			        	slidesToScroll: Number(lg_item_ar[1])
			      	}
			    },{
			      	breakpoint: 992,
			      	settings: {
			        	slidesToShow: Number(md_item_ar[0]),
			        	slidesToScroll: Number(md_item_ar[1])
			      	}
			    },{
			      	breakpoint: 768,
			      	settings: {
				        slidesToShow: Number(sm_item_ar[0]),
				        slidesToScroll: Number(sm_item_ar[1]),
				        vertical: false
			      	}
			    },{
			      	breakpoint: 480,
			      	settings: {
			        	slidesToShow: Number(xs_item_ar[0]),
			        	slidesToScroll: Number(xs_item_ar[1]),
			        	vertical: false
			      	}
				}];
			if(responsive==1){
				$(this).slick({
					dots: dots,
					infinite: infinite,
					arrows: arrows,
					speed: Number(speed),
					vertical: vertical,
					slidesToShow: Number(to_show),
					slidesToScroll: Number(to_scroll),
					autoplay: autoplay,
					autoplaySpeed: Number(autoplaySpeed),
					responsive: reponsive_json
				});
			}else{
				$(this).slick({
					dots: dots,
					infinite: infinite,
					arrows: arrows,
					speed: Number(speed),
					vertical: vertical,
					slidesToShow: Number(to_show),
					slidesToScroll: Number(to_scroll),
					autoplay: autoplay,
					autoplaySpeed: Number(autoplaySpeed)
				});
			}
		});
	}
};
NN_FRAMEWORK.Ckeditor = function () {
	if (isExist($(".form-control-ckeditor-chitiet"))) {
        $('.form-control-ckeditor-chitiet').each(function () {
            var id = $(this).attr('id');
            CKEDITOR.replace(id);
        });
        CKEDITOR.editorConfig = function (config) {
            config.language = 'vi';
            config.skin = 'moono-lisa';
            config.width = 'auto';
            config.height = 320;
            config.allowedContent = true;
            config.entities = false;
            config.entities_latin = false;
            config.entities_greek = false;
            config.basicEntities = false;
            config.contentsCss = [CONFIG_BASE + 'assets' + '/ckeditor/contents.css'];
            config.extraPlugins =
                '';
            config.line_height = '1;1.1;1.2;1.3;1.4;1.5;2;2.5;3;3.5;4;4.5;5';
            config.pasteFromWordRemoveFontStyles = false;
            config.pasteFromWordRemoveStyles = false;
            config.codemirror = {
                theme: 'default',
                lineNumbers: true,
                lineWrapping: true,
                matchBrackets: true,
                autoCloseTags: true,
                autoCloseBrackets: true,
                enableSearchTools: true,
                enableCodeFolding: true,
                enableCodeFormatting: true,
                autoFormatOnStart: true,
                autoFormatOnModeChange: true,
                autoFormatOnUncomment: true,
                mode: 'htmlmixed',
                showSearchButton: true,
                showTrailingSpace: true,
                highlightMatches: true,
                showFormatButton: true,
                showCommentButton: true,
                showUncommentButton: true,
                showAutoCompleteButton: true,
                styleActiveLine: true
            };
            
            config.toolbar = [
                {
                    name: 'clipboard',
                    items: ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo']
                },
                '/',
				{
					name: 'basicstyles',
					items: [
						'Bold',
						'Italic',
						'Underline',
						'Strike',
						'Subscript',
						'Superscript',
						'-',
						'CopyFormatting',
						'RemoveFormat'
					]
				},
				'/',
				{ name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize', 'lineheight'] },
				{ name: 'colors', items: ['TextColor', 'BGColor'] },
				'/',
				{
					name: 'links',
					items: ['Link', 'Unlink']
				},
            ];



            /* Config Wordcount */
            config.wordcount = {
                showParagraphs: true,
                showWordCount: true,
                showCharCount: true,
                countSpacesAsChars: false,
                countHTML: false,
                filter: new CKEDITOR.htmlParser.filter({
                    elements: {
                        div: function (element) {
                            if (element.attributes.class == 'mediaembed') {
                                return false;
}
                        }
                    }
                })
            };
        };
    }
};
/* Ready */
$(document).ready(function () {
	NN_FRAMEWORK.Homes();
	NN_FRAMEWORK.LoaderWrapper();
	NN_FRAMEWORK.SlickPage();
	NN_FRAMEWORK.AosAnimation();
	NN_FRAMEWORK.Lazys();
	NN_FRAMEWORK.Tools();
	NN_FRAMEWORK.Popup();
	NN_FRAMEWORK.Wows();
	NN_FRAMEWORK.AltImg();
	NN_FRAMEWORK.GoTop();
	NN_FRAMEWORK.Menu();
	NN_FRAMEWORK.OwlPage();
	NN_FRAMEWORK.Pagings();
	NN_FRAMEWORK.Cart();
	NN_FRAMEWORK.Videos();
	NN_FRAMEWORK.Photobox();
	NN_FRAMEWORK.Comment();
	NN_FRAMEWORK.Search();
	NN_FRAMEWORK.DomChange();
	/*NN_FRAMEWORK.TickerScroll();*/
	NN_FRAMEWORK.DatePicker();
	NN_FRAMEWORK.loadNameInputFile();
	NN_FRAMEWORK.QuickView();
	NN_FRAMEWORK.Toc();
	NN_FRAMEWORK.Ckeditor();
});
