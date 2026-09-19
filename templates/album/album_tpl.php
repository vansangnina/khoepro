<div class="title-main"><span><?=$titleMain?></span></div>
<?php /* if(isset($product) && count($product) > 0) { ?> 
        <div class="row flex-cus">
            <?php foreach($product as $k=>$v) { ?>
                <div class="pb-4 col-6 col-md-3 col-sm-4 mg-cus">
                    <div class="box-product" data-aos="fade-up" data-aos-duration="1000">
                        <p class="pic-product">
                            <a class="text-decoration-none scale-img" href="<?=$v[$sluglang]?>" title="<?=$v['name'.$lang]?>">
                                <img class="lazy w-100" onerror="this.src='<?=THUMBS?>/285x285x1/assets/images/noimage.png';" data-src="<?=THUMBS?>/285x285x2/<?=UPLOAD_PRODUCT_L.$v['photo']?>" alt="<?=$v['name'.$lang]?>" title="<?=$v['name'.$lang]?>"/>
                            </a>
                        </p>
                        <h3>
                            <a class="text-decoration-none name-product text-split" href="<?=$v[$sluglang]?>" title="<?=$v['name'.$lang]?>"><?=$v['name'.$lang]?></a>
                        </h3>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="pagination-home w-100"><?=(!empty($paging)) ? $paging : ''?></div>
<?php } else { ?>
    <div class="alert alert-warning" role="alert">
        <strong><?=khongtimthayketqua?></strong>
    </div>
<?php } */?>

<div class="scroll_tabnav_version3 tab-nav tab-fixed">
  <div class="container-fluid">
    <div class="row m-0">
      <div class="col-xl-12 p-0">
        <a href="#packages" class="">Xem các gói</a>
        <a href="#gioi-thieu" class="active">Giới thiệu</a>
        <a href="#visao" class="">Vì sao chọn P.A Việt Nam</a>
        <a href="#hoidapfaq" class="">Câu hỏi thường gặp</a>
      </div>
    </div>
  </div>
</div>
<section class="section_hosting dichvu_packages_version3 p-t-50 p-b-50" id="packages">
  <div class="container-fluid">
    <div class="pa_title_main_version3">
      <h2 class="m-0"></h2>
      <p>Hãy lựa chọn gói Hosting phù hợp với nhu cầu của bạn.</p>
    </div>
    <div class="row">
      <div class="col-xl-12">
        <div class="tabs-package">
          <button class="tablink svhost " onclick="">
            <a class="" href="/vn/hosting/cheap-hosting" title="Giá rẻ"> Giá rẻ <span>Cá nhân giá tốt</span>
            </a>
          </button>
          <button class="tablink unlimitedhost active" onclick="">
            <a class="selected" href="/vn/hosting/web-hosting" title="Chuyên nghiệp"> Chuyên nghiệp <span>Chi phí hợp lý</span>
            </a>
          </button>
          <button class="tablink hostingpricingpro " onclick="">
            <a class="" href="/vn/hosting/hosting-chat-luong-cao" title="Chất lượng cao"> Chất lượng cao <span>Không hài lòng hoàn tiền</span>
            </a>
          </button>
          <button class="tablink shareserver " onclick="">
            <a class="" href="/vn/hosting/super-hosting" title="Super"> Super <span>Lượng truy cập lớn</span>
            </a>
          </button>
          <button class="tablink resellerhost " onclick="">
            <a class="" href="/vn/hosting/reseller-hosting" title="Reseller"> Reseller <span>Dễ dàng chia nhỏ</span>
            </a>
          </button>
          <button class="tablink dedicatehost " onclick="">
            <a class="" href="/vn/hosting/dedicated-hosting" title="Dedicated"> Dedicated <span>Hosting IP riêng</span>
            </a>
          </button>
        </div>
        <script type="text/javascript" src="https://www.pavietnam.vn/js/swiper-bundle.min.js"></script>
        <link rel="stylesheet" href="https://www.pavietnam.vn/css/swiper-bundle.min.css">
        <div class="swiper mySwiperMenu swiper-initialized swiper-horizontal swiper-free-mode">
          <div class="tabs-package_v2 swiper-wrapper" id="swiper-wrapper-f2f45c90554f3e14" aria-live="polite" style="transition-duration: 300ms;">
            <a href="/vn/hosting/cheap-hosting" class="svhost  swiper-slide"> Giá rẻ <span>Cá nhân giá tốt</span>
            </a>
            <a href="/vn/hosting/web-hosting" class="unlimitedhost active swiper-slide"> Chuyên nghiệp <span>Chi phí hợp lý</span>
            </a>
            <a href="/vn/hosting/hosting-chat-luong-cao" class="hostingpricingpro  swiper-slide"> Chất lượng cao <span>Không hài lòng hoàn tiền</span>
            </a>
            <a href="/vn/hosting/super-hosting" class="shareserver  swiper-slide"> Super <span>Lượng truy cập lớn</span>
            </a>
            <a href="/vn/hosting/reseller-hosting" class="resellerhost  swiper-slide"> Reseller <span>Dễ dàng chia nhỏ</span>
            </a>
            <a href="/vn/hosting/dedicated-hosting" class="dedicatehost  swiper-slide"> Dedicated <span>Hosting IP riêng</span>
            </a>
          </div>
          <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
          <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
        </div>
        <style>
          @media screen and (min-width: 768px) {
            .mySwiperMenu {
              display: none;
            }
          }

          @media screen and (max-width: 768px) {
            .mySwiperMenu {
              display: block;
            }

            .dichvu_packages_version3 .tabs-package {
              display: none !important;
            }
          }

          /* width */
          .tabs-package_v2::-webkit-scrollbar {
            height: 4px;
          }

          /* Track */
          .tabs-package_v2::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
          }

          /* Handle */
          .tabs-package_v2::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
          }

          /* Handle on hover */
          .tabs-package_v2::-webkit-scrollbar-thumb:hover {
            background: #555;
          }

          /* width */
          .tabs-package_v2::-webkit-scrollbar {
            height: 4px;
          }

          /* Track */
          .tabs-package_v2::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
          }

          /* Handle */
          .tabs-package_v2::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
          }

          /* Handle on hover */
          .tabs-package_v2::-webkit-scrollbar-thumb:hover {
            background: #555;
          }

          .tabs-package_v2>a {
            padding: 8px 10px;
            border-radius: 5px;
            text-align: center;
            display: inline-block;
            background: #f3f3f3;
            color: #333;
            font-family: "Open Sans", sans-serif;
            font-weight: 700;
            margin: 0 5px 10px 0;
            line-height: 25px;
            white-space: nowrap;
            text-align: center;
            width: auto !important;
          }

          .tabs-package_v2>a.active {
            color: #fff;
            background: #231f20;
          }

          .tabs-package_v2>a>span {
            display: block;
            font-weight: 400;
            font-size: 13px;
          }
        </style>
        <script>
          var swiper = new Swiper(".mySwiperMenu", {
            slidesPerView: 2.5,
            spaceBetween: 5,
            freeMode: true,
            pagination: {
              clickable: true,
            },
          });
        </script>
        <div class="tab-panel">
          <div class="owl-carousel owl-theme owl-custome package-version3 owl-loaded owl-drag">
            <div class="owl-stage-outer">
              <div class="owl-stage" style="transform: translate3d(-1340px, 0px, 0px); transition: 1s; width: 2681px;">
                <div class="owl-item" style="width: 426.667px; margin-right: 20px;">
                  <div id="plan_32507" class="757 package_item style_3">
                    <h3> Cơ Bản </h3>
                    <ul class="gia">
                      <li class="only">
                        <strong>Giảm 25%</strong> 44.000đ
                      </li>
                      <li class="vnd">
                        <strong>33.000</strong>
                        <sup>đ</sup>
                        <span class="sub">/tháng</span>
                      </li>
                      <li class="timeplan"> với thời hạn 03 năm </li>
                    </ul>
                    <a class="datmua_btn btn" rel="nofollow" href="/vn/dang-ky-unix-32507.html" data-submit="unix-32507-757">Đăng ký ngay</a>
                    <table>
                      <thead></thead>
                      <tbody>
                        <tr>
                          <td>Sức Mạnh <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>
                            <img src="/images/hosting_v2/power_3.svg" style="width: 30px;height: 30px;float: right;">
                          </td>
                        </tr>
                        <tr>
                          <td> SSD NVME <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>2 GB</td>
                        </tr>
                        <tr>
                          <td> Băng thông <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn</td>
                        </tr>
                        <tr>
                          <td> Địa chỉ Email <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>10</td>
                        </tr>
                        <tr>
                          <td> Database <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>3</td>
                        </tr>
                        <tr>
                          <td> Park/Addon Domain <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>1/0</td>
                        </tr>
                        <tr>
                          <td> SSL <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Miễn phí</td>
                        </tr>
                        <tr>
                          <td> Web server <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>
                            <a href="https://kb.pavietnam.vn/litespeed-la-gi-loi-ich-khi-su-dung-litespeed.html" style="color: #000;text-transform: unset;font-weight: normal;">Litespeed</a> | <a href=" https://kb.pavietnam.vn/iis-la-gi-nhung-dieu-co-ban-ban-can-biet.html" style="color: #000;text-transform: unset;font-weight: normal;">IIS</a>
                          </td>
                        </tr>
                        <tr>
                          <td> Control Panel </td>
                          <td>cPanel | Plesk</td>
                        </tr>
                        <tr>
                          <td> Tiêu chuẩn chất lượng </td>
                          <td>
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="owl-item" style="width: 426.667px; margin-right: 20px;">
                  <div id="plan_31340" class="470 package_item style_3">
                    <h3> Cá Nhân </h3>
                    <ul class="gia">
                      <li class="only">
                        <strong>Giảm 22%</strong> 78.000đ
                      </li>
                      <li class="vnd">
                        <strong>61.000</strong>
                        <sup>đ</sup>
                        <span class="sub">/tháng</span>
                      </li>
                      <li class="timeplan"> với thời hạn 03 năm </li>
                    </ul>
                    <a class="datmua_btn btn" rel="nofollow" href="/vn/dang-ky-unix-31340.html" data-submit="unix-31340-470">Đăng ký ngay</a>
                    <table>
                      <thead></thead>
                      <tbody>
                        <tr>
                          <td>Sức Mạnh <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>
                            <img src="/images/hosting_v2/power_3.svg" style="width: 30px;height: 30px;float: right;">
                          </td>
                        </tr>
                        <tr>
                          <td> SSD NVME <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>4 GB</td>
                        </tr>
                        <tr>
                          <td> Băng thông <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn</td>
                        </tr>
                        <tr>
                          <td> Địa chỉ Email <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>20</td>
                        </tr>
                        <tr>
                          <td> Database <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>5</td>
                        </tr>
                        <tr>
                          <td> Park/Addon Domain <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn/1</td>
                        </tr>
                        <tr>
                          <td> SSL <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Miễn phí</td>
                        </tr>
                        <tr>
                          <td> Web server <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>
                            <a href="https://kb.pavietnam.vn/litespeed-la-gi-loi-ich-khi-su-dung-litespeed.html" style="color: #000;text-transform: unset;font-weight: normal;">Litespeed</a> | <a href=" https://kb.pavietnam.vn/iis-la-gi-nhung-dieu-co-ban-ban-can-biet.html" style="color: #000;text-transform: unset;font-weight: normal;">IIS</a>
                          </td>
                        </tr>
                        <tr>
                          <td> Control Panel </td>
                          <td>cPanel | Plesk</td>
                        </tr>
                        <tr>
                          <td> Tiêu chuẩn chất lượng </td>
                          <td>
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="owl-item" style="width: 426.667px; margin-right: 20px;">
                  <div id="plan_31257" class="448 package_item style_3">
                    <h3> Bán Chuyên Nghiệp </h3>
                    <ul class="gia">
                      <li class="only">
                        <strong>Giảm 22%</strong> 123.000đ
                      </li>
                      <li class="vnd">
                        <strong>96.000</strong>
                        <sup>đ</sup>
                        <span class="sub">/tháng</span>
                      </li>
                      <li class="timeplan"> với thời hạn 03 năm </li>
                    </ul>
                    <a class="datmua_btn btn" rel="nofollow" href="/vn/dang-ky-unix-31257.html" data-submit="unix-31257-448">Đăng ký ngay</a>
                    <table>
                      <thead></thead>
                      <tbody>
                        <tr>
                          <td>Sức Mạnh <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>
                            <img src="/images/hosting_v2/power_4.svg" style="width: 30px;height: 30px;float: right;">
                          </td>
                        </tr>
                        <tr>
                          <td> SSD NVME <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>8 GB</td>
                        </tr>
                        <tr>
                          <td> Băng thông <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn</td>
                        </tr>
                        <tr>
                          <td> Địa chỉ Email <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn</td>
                        </tr>
                        <tr>
                          <td> Database <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>10</td>
                        </tr>
                        <tr>
                          <td> Park/Addon Domain <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn/2</td>
                        </tr>
                        <tr>
                          <td> SSL <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Miễn phí</td>
                        </tr>
                        <tr>
                          <td> Web server <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>
                            <a href="https://kb.pavietnam.vn/litespeed-la-gi-loi-ich-khi-su-dung-litespeed.html" style="color: #000;text-transform: unset;font-weight: normal;">Litespeed</a> | <a href=" https://kb.pavietnam.vn/iis-la-gi-nhung-dieu-co-ban-ban-can-biet.html" style="color: #000;text-transform: unset;font-weight: normal;">IIS</a>
                          </td>
                        </tr>
                        <tr>
                          <td> Control Panel </td>
                          <td>cPanel | Plesk</td>
                        </tr>
                        <tr>
                          <td> Tiêu chuẩn chất lượng </td>
                          <td>
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="owl-item active" style="width: 426.667px; margin-right: 20px;">
                  <div id="plan_31259" class="449 package_item style_3">
                    <h3> Chuyên Nghiệp </h3>
                    <ul class="gia">
                      <li class="only">
                        <strong>Giảm 22%</strong> 179.000đ
                      </li>
                      <li class="vnd">
                        <strong>140.000</strong>
                        <sup>đ</sup>
                        <span class="sub">/tháng</span>
                      </li>
                      <li class="timeplan"> với thời hạn 03 năm </li>
                    </ul>
                    <a class="datmua_btn btn" rel="nofollow" href="/vn/dang-ky-unix-31259.html" data-submit="unix-31259-449">Đăng ký ngay</a>
                    <table>
                      <thead></thead>
                      <tbody>
                        <tr>
                          <td>Sức Mạnh <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>
                            <img src="/images/hosting_v2/power_4.svg" style="width: 30px;height: 30px;float: right;">
                          </td>
                        </tr>
                        <tr>
                          <td> SSD NVME <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>12 GB</td>
                        </tr>
                        <tr>
                          <td> Băng thông <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn</td>
                        </tr>
                        <tr>
                          <td> Địa chỉ Email <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn</td>
                        </tr>
                        <tr>
                          <td> Database <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>15</td>
                        </tr>
                        <tr>
                          <td> Park/Addon Domain <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn/3</td>
                        </tr>
                        <tr>
                          <td> SSL <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Miễn phí</td>
                        </tr>
                        <tr>
                          <td> Web server <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>
                            <a href="https://kb.pavietnam.vn/litespeed-la-gi-loi-ich-khi-su-dung-litespeed.html" style="color: #000;text-transform: unset;font-weight: normal;">Litespeed</a> | <a href=" https://kb.pavietnam.vn/iis-la-gi-nhung-dieu-co-ban-ban-can-biet.html" style="color: #000;text-transform: unset;font-weight: normal;">IIS</a>
                          </td>
                        </tr>
                        <tr>
                          <td> Control Panel </td>
                          <td>cPanel | Plesk</td>
                        </tr>
                        <tr>
                          <td> Tiêu chuẩn chất lượng </td>
                          <td>
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="owl-item active" style="width: 426.667px; margin-right: 20px;">
                  <div id="plan_31261" class="450 package_item style_3">
                    <h3> Doanh Nghiệp </h3>
                    <ul class="gia">
                      <li class="only">
                        <strong>Giảm 22%</strong> 291.000đ
                      </li>
                      <li class="vnd">
                        <strong>227.000</strong>
                        <sup>đ</sup>
                        <span class="sub">/tháng</span>
                      </li>
                      <li class="timeplan"> với thời hạn 03 năm </li>
                    </ul>
                    <a class="datmua_btn btn" rel="nofollow" href="/vn/dang-ky-unix-31261.html" data-submit="unix-31261-450">Đăng ký ngay</a>
                    <table>
                      <thead></thead>
                      <tbody>
                        <tr>
                          <td>Sức Mạnh <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>
                            <img src="/images/hosting_v2/power_5.svg" style="width: 30px;height: 30px;float: right;">
                          </td>
                        </tr>
                        <tr>
                          <td> SSD NVME <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>16 GB</td>
                        </tr>
                        <tr>
                          <td> Băng thông <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn</td>
                        </tr>
                        <tr>
                          <td> Địa chỉ Email <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn</td>
                        </tr>
                        <tr>
                          <td> Database <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>20</td>
                        </tr>
                        <tr>
                          <td> Park/Addon Domain <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn/6</td>
                        </tr>
                        <tr>
                          <td> SSL <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Miễn phí</td>
                        </tr>
                        <tr>
                          <td> Web server <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>
                            <a href="https://kb.pavietnam.vn/litespeed-la-gi-loi-ich-khi-su-dung-litespeed.html" style="color: #000;text-transform: unset;font-weight: normal;">Litespeed</a> | <a href=" https://kb.pavietnam.vn/iis-la-gi-nhung-dieu-co-ban-ban-can-biet.html" style="color: #000;text-transform: unset;font-weight: normal;">IIS</a>
                          </td>
                        </tr>
                        <tr>
                          <td> Control Panel </td>
                          <td>cPanel | Plesk</td>
                        </tr>
                        <tr>
                          <td> Tiêu chuẩn chất lượng </td>
                          <td>
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="owl-item active" style="width: 426.667px; margin-right: 20px;">
                  <div id="plan_31342" class="471 package_item style_3">
                    <h3> Thương Mại Điện Tử </h3>
                    <ul class="gia">
                      <li class="only">
                        <strong>Giảm 22%</strong> 483.000đ
                      </li>
                      <li class="vnd">
                        <strong>377.000</strong>
                        <sup>đ</sup>
                        <span class="sub">/tháng</span>
                      </li>
                      <li class="timeplan"> với thời hạn 03 năm </li>
                    </ul>
                    <a class="datmua_btn btn" rel="nofollow" href="/vn/dang-ky-unix-31342.html" data-submit="unix-31342-471">Đăng ký ngay</a>
                    <table>
                      <thead></thead>
                      <tbody>
                        <tr>
                          <td>Sức Mạnh <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>
                            <img src="/images/hosting_v2/power_5.svg" style="width: 30px;height: 30px;float: right;">
                          </td>
                        </tr>
                        <tr>
                          <td> SSD NVME <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>25 GB</td>
                        </tr>
                        <tr>
                          <td> Băng thông <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn</td>
                        </tr>
                        <tr>
                          <td> Địa chỉ Email <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn</td>
                        </tr>
                        <tr>
                          <td> Database <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>25</td>
                        </tr>
                        <tr>
                          <td> Park/Addon Domain <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Không giới hạn/8</td>
                        </tr>
                        <tr>
                          <td> SSL <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>Miễn phí</td>
                        </tr>
                        <tr>
                          <td> Web server <div style="margin-left: 5px" class="iquestion xemmota tooltipstered"></div>
                          </td>
                          <td>
                            <a href="https://kb.pavietnam.vn/litespeed-la-gi-loi-ich-khi-su-dung-litespeed.html" style="color: #000;text-transform: unset;font-weight: normal;">Litespeed</a> | <a href=" https://kb.pavietnam.vn/iis-la-gi-nhung-dieu-co-ban-ban-can-biet.html" style="color: #000;text-transform: unset;font-weight: normal;">IIS</a>
                          </td>
                        </tr>
                        <tr>
                          <td> Control Panel </td>
                          <td>cPanel | Plesk</td>
                        </tr>
                        <tr>
                          <td> Tiêu chuẩn chất lượng </td>
                          <td>
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                            <img src="/images/hosting/ngoisao.svg" alt="ngoi sao" style="width: 15px;float: right;padding-right: 5px">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="owl-nav">
              <button type="button" role="presentation" class="owl-prev">
                <span aria-label="Pre"></span>
              </button>
              <button type="button" role="presentation" class="owl-next">
                <span aria-label="Next"></span>
              </button>
            </div>
            <div class="owl-dots">
              <button role="button" class="owl-dot">
                <span></span>
              </button>
              <button role="button" class="owl-dot active">
                <span></span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-12">
      <div class="note">
        <ul>
          <li>- Giá chưa gồm VAT</li>
          <li>- Lưu ý về tài nguyên sử dụng&nbsp; <div class="iquestion xemmota tooltipstered"></div>
          </li>
          <li>- Tham khảo dịch vụ sao lưu dữ liệu : <a href="https://www.pavietnam.vn/vn/webhost-backup.html" title="P.ACKUP Cpanel" style="color:#00F"> xem tại đây</a>
          </li>
          <li>- Thông số Hosting đuợc áp dụng từ 29-01-2018</li>
          <li>- Hỗ trợ phiên bản "Mysql Community Edition"</li>
        </ul>
      </div>
    </div>
  </div>
</section>
<section id="gioi-thieu" class="section_hosting dichvu_gioithieu p-b-50">
  <div class="container-fluid">
    <div class="pa_title_main">
      <h2>GIỚI THIỆU </h2>
    </div>
    <div class="row">
      <div class="col-xl-4 col-md-6 col-sm-12 p-b-20">
        <div class="gioithieu_box" style="background: #f1e3d7;">
          <h3>SIÊU TỐC</h3>
          <p>Máy chủ sử dụng các dòng CPU thế hệ mới nhất của Intel công nghệ lưu trữ NVMe SSD U.2 - Raid 10 cực nhanh và an toàn dữ liệu cao nhất.</p>
        </div>
      </div>
      <div class="col-xl-4 col-md-6 col-sm-12 p-b-20">
        <div class="gioithieu_box" style="background: #e3e4dd;">
          <h3>HOẠT ĐỘNG 99.99%</h3>
          <p>Với đội ngũ giám sát 24/7 <br>Công nghệ clustering đặc biệt giúp hệ thống luôn hoạt động liên tục. </p>
        </div>
      </div>
      <div class="col-xl-4 col-md-6 col-sm-12 p-b-20">
        <div class="gioithieu_box" style="background: #f4f8fc;">
          <h3>DỄ DÀNG SỬ DỤNG</h3>
          <p>Cung cấp hệ thống quản trị tốt nhất thế giới giúp khách hàng không có kiến thức kỹ thuật vẫn khai thác dịch vụ hiệu quả.</p>
        </div>
      </div>
      <div class="col-xl-4 col-md-6 col-sm-12 p-b-20">
        <div class="gioithieu_box" style="background: #f4f8fc;">
          <h3>CÓ BẢN QUYỀN</h3>
          <p>P.A Việt Nam là đơn vị đầu tiên tại VN cam kết sử dụng Phần mềm có bản quyền cung cấp dịch vụ cho khách hàng.</p>
        </div>
      </div>
      <div class="col-xl-4 col-md-6 col-sm-12 p-b-20">
        <div class="gioithieu_box" style="background: #eaedf2;">
          <h3>BẢO MẬT &amp; AN TOÀN</h3>
          <p>Ứng dụng công nghệ AI trong việc giám sát, quản lý, giảm thiểu hạn chế các mối nguy hại từ môi trường bên trong lẫn bên ngoài.</p>
        </div>
      </div>
      <div class="col-xl-4 col-md-6 col-sm-12 p-b-20">
        <div class="gioithieu_box" style="background: #f1e3e4;">
          <h3>HỖ TRỢ KỸ THUẬT 24/7</h3>
          <p>Là nhà cung cấp dịch vụ lớn và uy tín trên 23 năm chúng tôi luôn có đội ngũ hỗ trợ khách hàng 24/7 để xử lý mọi tình huống trong quá trình sử dụng.</p>
        </div>
      </div>
    </div>
  </div>
</section>
<section id="visao" class="section_hosting dichvu_visao_version3 p-t-50 p-b-50">
  <div class="container-fluid">
    <div class="pa_title_main_version3">
      <h2>Vì sao nên chọn </h2>
    </div>
    <div class="row m-0">
      <div class="col-xl-3 col-md-6 col-sm-12">
        <div class="visao_box standard">
          <img style="width: auto;" src="https://support.pavietnam.vn/datafile/banner/2024_04/538544-13110258-icon7.png" height="40">
          <h3>Giá tốt nhất thị trường</h3>
          <p style="padding-left: 0;">Chúng tôi cam kết giá tại đây luôn tốt nhất cho bạn</p>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 col-sm-12">
        <div class="visao_box standard">
          <img style="width: auto;" src="https://support.pavietnam.vn/datafile/banner/2024_04/538544-13110427-icon8.png" height="40">
          <h3>Đáng tin cậy</h3>
          <p style="padding-left: 0;">Giữ vị trí top 1 số lượng đăng ký .VN hơn 2 thập kỉ qua, số liệu được cung cấp bởi Trung Tâm Internet Việt Nam VNNIC</p>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 col-sm-12">
        <div class="visao_box standard">
          <img style="width: auto;" src="https://support.pavietnam.vn/datafile/banner/2024_04/538544-13110442-icon9.png" height="40">
          <h3>23 năm dẫn đầu</h3>
          <p style="padding-left: 0;">Hơn 23 năm trong lĩnh vực cung cấp tên miền, lưu trữ website, email cho doanh nghiệp , máy chủ đám mây và các giải pháp dịch vụ giá trị gia tăng Internet</p>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 col-sm-12">
        <div class="visao_box standard">
          <img style="width: auto;" src="https://support.pavietnam.vn/datafile/banner/2024_04/538544-13110459-icon10.png" height="40">
          <h3>Hỗ trợ 24/7</h3>
          <p style="padding-left: 0;">Trung tâm hỗ trợ khách hàng của chúng tôi luôn hiện diện trực tuyến và sẵn sàng trợ giúp bạn mọi lúc, hãy gọi ngay 1900 9477</p>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="section_hosting dichvu_cauhoi_version3 p-t-50 p-b-50" id="hoidapfaq">
  <div class="container-fluid">
    <div class="pa_title_main_version3">
      <h2>Câu hỏi thường gặp</h2>
    </div>
    <div class="accordion">
      <div class="accordion-item">
        <button id="accordion-button-0" aria-expanded="false">
          <span class="accordion-title">Hosting là gì?</span>
          <span class="icon" aria-hidden="true"></span>
        </button>
        <div class="accordion-content">
          <p>
            <strong></strong> được gọi chung là <strong>Hosting</strong>, là dịch vụ lưu trữ dữ liệu và chia sẻ trực tuyến. Khi bạn đăng ký dịch vụ Hosting, tức là bạn thuê một chỗ đặt trên server chứa tất cả các files và dữ liệu cần thiết để website của bạn chạy được. <br>Hiểu theo một cách đơn giản thì nếu website là một ngôi nhà, tên miền (domain) là địa chỉ ngôi nhà thì <strong>Hosting</strong> chính là mảnh đất mà ngôi nhà đó được xây dựng lên. Hosting cũng chính là nơi diễn ra tất cả các hoạt động giao dịch, trao đổi thông tin giữa người sử dụng internet với website, hỗ trợ các phần mềm internet hoạt động.
          </p>
        </div>
      </div>
      <div class="accordion-item">
        <button id="accordion-button-1" aria-expanded="false">
          <span class="accordion-title">Tại sao cần phải mua </span>
          <span class="icon" aria-hidden="true"></span>
        </button>
        <div class="accordion-content">
          <p>Nếu không có  thì website sẽ chỉ hoạt động trên máy tính bạn mà thôi, duy chỉ có mình bạn nhìn thấy, dữ liệu sẽ không được chia sẻ trên mạng. Cho nên rất cần thiết để có một gói Hosting.</p>
        </div>
      </div>
      <div class="accordion-item">
        <button id="accordion-button-2" aria-expanded="false">
          <span class="accordion-title">Mua  ở đâu uy tín?</span>
          <span class="icon" aria-hidden="true"></span>
        </button>
        <div class="accordion-content">
          <p>Bạn có thể dùng  nước ngoài hoặc Việt Nam. Nếu website bạn chủ yếu có lượt truy cập trong nước thì nên chọn mua Hosting Việt Nam là tốt nhất. <br>Có nhiều nhà cung cấp Hosting bạn có thể chọn, trong đó <strong>công ty P.A Việt Nam</strong> có hơn 23 năm trong lĩnh vực tên miền, Hosting. Là một trong những nhà cung cấp dịch vụ Hosting uy tín hàng đầu tại Việt Nam. </p>
        </div>
      </div>
      <div class="accordion-item">
        <button id="accordion-button-3" aria-expanded="false">
          <span class="accordion-title"> gồm những loại nào?</span>
          <span class="icon" aria-hidden="true"></span>
        </button>
        <div class="accordion-content">
          <p>Có nhiều loại Hosting với đa dạng tính năng khác nhau trên thị trường. <strong>Dedicated Web Hosting</strong> và <strong>Cloud Hosting</strong> là hai loại mô hình hosting được lựa chọn sử dụng nhiều nhất. <br>+ <strong>Dedicated Web Hosting</strong> là hình thức lưu trữ web phổ biến nhất. Với chi phí bỏ ra hợp lý bạn đã có dịch vụ đáp ứng hầu hết các nhu cầu lưu trữ website của mình. Dịch vụ Web Hosting P.A cung cấp dùng trên phần cứng thật giúp tối ưu và đạt tốc độ cao nhất thay vì dùng ảo hóa. Dịch vụ Share Hosting phù hợp với một doanh nghiệp sử dụng web để bán hàng và những tổ chức vừa có lượt truy cập không quá lớn. <br>+ <strong>Cloud Hosting</strong> là loại hosting chạy trên nền tảng ảo hóa với Cloud Hosting, bạn có máy chủ chuyên dụng nhưng máy chủ là máy ảo chứ không phải là máy vật lý. Điều này mang đến lợi ích cho người quản lý khi tiết kiệm chi phí quản lý,bảo trì, nâng cấp phần cứng nhưng lại giảm một phần tốc độ xử lý so với dùng trực tiếp phần cứng thật. Cloud Hosting cũng phù hợp với một doanh nghiệp sử dụng web để bán hàng và những tổ chức vừa có lượt truy cập không quá lớn. </p>
        </div>
      </div>
      <div class="accordion-item">
        <button id="accordion-button-4" aria-expanded="false">
          <span class="accordion-title">Các yếu tố đánh giá một Hosting?</span>
          <span class="icon" aria-hidden="true"></span>
        </button>
        <div class="accordion-content">
          <p>Một Hosting tốt được đánh giá dựa trên các yếu tố như <br>+ <strong>Tốc độ</strong>: Là khoảng thời gian tính từ khi người dùng internet bắt đầu truy cập vào trang web đến khi nội dung trên web được tải về hoàn toàn. Lý tưởng từ 3 đến 5 giây. <br>+ <strong>Dung lượng</strong>: Là dung lượng lưu trữ (Disk space) – khoảng không gian trong ổ cứng máy chủ bạn được sử dụng để lưu trữ dữ liệu. <br>+ <strong>Băng thông</strong>: Là lượng dữ liệu trao đổi giữa trang web với người dùng internet trong một tháng. <br>+ <strong>Khả năng chịu tải</strong>: Là khả năng chấp nhận số người online trong cùng một thời điểm. <br>+ <strong>Dịch vụ hỗ trợ</strong> của đơn vị cung cấp Hosting. </p>
        </div>
      </div>
      <div class="accordion-item">
        <button id="accordion-button-5" aria-expanded="false">
          <span class="accordion-title">Hosting có giới hạn số lượng khách truy cập đồng thời trên website không?</span>
          <span class="icon" aria-hidden="true"></span>
        </button>
        <div class="accordion-content">
          <p>Hosting P.A Việt Nam không giới hạn về số lượng khách truy cập đồng thời đối với website của bạn. <br>Tuy nhiên, có những giới hạn thực tế liên quan đến CPU, RAM và Entry Process ( Tác vụ xử lý đồng thời ) được quy định tùy vào phân loại Hosting. <br>Mỗi website là khác nhau, được lập trình và thiết kế khác nhau, sử dụng tài nguyên khác nhau. Do đó, không có cách nào để đảm bảo rằng trang web của bạn có thể đáp ứng số lượng khách truy cập tối đa. <br>Một website được tối ưu tốt, sử dụng ít tài nguyên trên mỗi lượt khách truy cập sẽ cho phép số lượng lớn khách truy cập đồng thời hơn. <br>Ngược lại, một website không được tối ưu tốt hoặc kém hiểu quả thì chỉ có thể đáp ứng duy trì được số lượng ít khách truy cập đồng thời. </p>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
    .scroll_tabnav_version3.tab-nav.tab-fixed {
        position: fixed;
        top: 0;
        z-index: 9999999;
        background: #fff;
        padding: 5px 0;
        -webkit-box-shadow: 0 5px 18px rgba(0, 0, 0, 0.15);
        -moz-box-shadow: 0 5px 18px rgba(0, 0, 0, 0.15);
        box-shadow: 0 5px 18px rgba(0, 0, 0, 0.15);
    }
    .scroll_tabnav_version3.tab-nav a {
        display: inline-block;
        width: auto;
        color: #333;
        line-height: 20px;
        font-size: 14px;
        font-weight: 500;
        padding: 10px 20px;
        border-radius: 50px;
        text-align: center;
        transition: all .2s ease;
        padding: 5px 20px;
    }
    .scroll_tabnav_version3.tab-nav a.active,.scroll_tabnav_version3.tab-nav a:hover {
        color: #fff;
        background: #231f20;
    }
</style>