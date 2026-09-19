<style>
    .seo-panel-group ul{
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .seo-panel-group ul li {
        font-size: 15px;
        line-height: 28px;
        position: relative;
        clear: both;
        color: #5a6065;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }
    .seo-panel-group ul li:last-child{margin-bottom: 0;}
    li span.icon {
        color:#fff;
        width: 25px; height: 25px; line-height: 25px;
        text-align: center;
        list-style: none;
        border-radius: 50%;
        margin-right: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    li.test-fail span.icon {
        background-color: #df0000;
    }
    li.test-success span.icon {
        background-color: #04ad11;
    }
</style>
<!-- SEO -->
<?php
$slugurlArray = '';
$seo_create = '';
if (($com == "static" || $com == "seopage") && isset($config['website']['comlang'])) {
    foreach ($config['website']['comlang'] as $k => $v) {
        if ($type == $k) {
            $slugurlArray = $v;
            break;
        }
    }
}
$listCriteria = [
    'keywordNotUsed' => 'Đặt Từ khóa tập trung cho nội dung này.',
    'keywordInTitle' => 'Thêm từ khóa chính vào tiêu đề SEO.',
    'titleStartWithKeyword' => 'Sử dụng từ khóa chính gần đầu tiêu đề SEO.',
    'lengthTitle' => 'Tiêu đề của bài viết phải lớn hơn 40 ký tự và khuyến cáo nhỏ hơn 70 ký tự',
    'keywordInMetaDescription' => 'Thêm Từ khóa tập trung vào Mô tả meta SEO của bạn.',
    'lengthMetaDescription' => 'Mô tả meta SEO của bạn nên có từ 155 đến 160 ký tự.',
    'keywordInPermalink' => 'Sử dụng từ khóa chính trong URL.',
    'lengthPermalink' => 'URL không khả dụng. Thêm URL ngắn.',
    'keywordIn10Percent' => 'Sử dụng từ khóa chính ở đầu nội dung của bạn.',
    'keywordInContent' => 'Sử dụng từ khóa chính trong nội dung.',
    'lengthContent' => 'Nội dung phải dài 600-2500 từ.',
    'linksHasInternal' => 'Thêm liên kết nội bộ vào nội dung của bạn.',
    'keywordInSubheadings' => 'Sử dụng từ khóa chính trong (các) tiêu đề phụ như H2, H3, H4, v.v..',
    'keywordInImageAlt' => 'Thêm từ khóa vào thuộc tính alt của hình ảnh',
    'keywordDensity' => 'Mật độ từ khóa là 0. Nhắm đến khoảng 1% Mật độ từ khóa.',
    'contentHasShortParagraphs' => 'Thêm các đoạn văn ngắn và súc tích để dễ đọc và UX hơn.',
    'contentHasAssets' => 'Thêm một vài hình ảnh để làm cho nội dung của bạn hấp dẫn.',
];
$schema = false;
if(!empty($config[$com][$type]['schema'])) $schema = true;
?>
<div class="card-seo" <?php if(!empty($schema)){?>x-data="seoRankMath()" x-init="init()" <?php }?>>
    <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="custom-tabs-three-tab-lang" role="tablist">
                <?php foreach ($config['website']['seo'] as $k => $v) {
                    $seo_create .= $k . ","; ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($k == 'vi') ? 'active' : '' ?>" id="tabs-lang" data-toggle="pill" href="#tabs-seolang-<?= $k ?>" role="tab" aria-controls="tabs-seolang-<?= $k ?>" aria-selected="true">SEO (<?= $v ?>)</a>
                    </li>
                <?php } ?>
                <?php /*
                <li class="nav-item">
                    <a class="nav-link" id="tabs-lang" data-toggle="pill" href="#tabs-seonangcao" role="tab" aria-controls="tabs-seonangcao" aria-selected="true">Nâng cao</a>
                </li>
                */ ?>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-three-tabContent-lang">
                <?php foreach ($config['website']['seo'] as $k => $v) { ?>
                    <div class="tab-pane fade show <?= ($k == 'vi') ? 'active' : '' ?>" id="tabs-seolang-<?= $k ?>" role="tabpanel" aria-labelledby="tabs-lang">
                        <div class="form-group">
                            <div class="label-seo">
                                <label for="title<?= $k ?>">SEO Title (<?= $k ?>):</label>
                                <strong class="count-seo"><span><?= strlen(htmlspecialchars(@$seoDB['title' . $k]?:'')) ?></span>/70 ký tự</strong>
                            </div>
                            <input @keyup.debounce.50ms="seoRankMathGroup()" type="text" class="form-control check-seo title-seo text-sm" name="dataSeo[title<?= $k ?>]" id="title<?= $k ?>" placeholder="SEO Title (<?= $k ?>)" value="<?= (!empty($flash->has('title' . $k))) ? $flash->get('title' . $k) : @$seoDB['title' . $k] ?>">
                        </div>
                        <div class="form-group">
                            <div class="label-seo">
                                <label for="keywords<?= $k ?>">SEO Keywords (<?= $k ?>):</label>
                                <strong class="count-seo"><span><?= strlen(htmlspecialchars(@$seoDB['keywords' . $k]?:'')) ?></span>/70 ký tự</strong>
                            </div>
                            <input type="text" class="form-control check-seo keywords-seo text-sm" name="dataSeo[keywords<?= $k ?>]" id="keywords<?= $k ?>" placeholder="SEO Keywords (<?= $k ?>)" value="<?= (!empty($flash->has('keywords' . $k))) ? $flash->get('keywords' . $k) : @$seoDB['keywords' . $k] ?>">
                        </div>
                        <div class="form-group">
                            <div class="label-seo">
                                <label for="description<?= $k ?>">SEO Description (<?= $k ?>):</label>
                                <strong class="count-seo"><span><?= strlen(htmlspecialchars(@$seoDB['description' . $k] ?: '')) ?></span>/160 ký tự</strong>
                            </div>
                            <textarea x-model="description['<?=$k?>'].value" @keyup.debounce.50ms="seoRankMathGroup()" class="form-control check-seo description-seo text-sm" name="dataSeo[description<?= $k ?>]" id="description<?= $k ?>" rows="5" placeholder="SEO Description (<?= $k ?>)"><?= $func->decodeHtmlChars($flash->get('description' . $k)) ?: $func->decodeHtmlChars(@$seoDB['description' . $k]) ?></textarea>
                        </div>
                        <?php if (isset($config[$com][$type]['schema']) && $config[$com][$type]['schema'] == true) { ?>
                        <div class="form-group">
                            <div class="label-seo">
                                <label for="main_keywords<?= $k ?>">Keyword chính:</label>
                            </div>
                            <div class="input-group">
                                <input type="text" x-model="keyword['<?=$k?>'].value" @keyup.debounce.50ms="seoRankMathGroup()" class="form-control text-sm seo_focus_keyword" name="dataSeo[main_keywords<?= $k ?>]" id="main_keywords<?= $k ?>" placeholder="Keyword chính" value="<?= (!empty($flash->has('main_keywords' . $k))) ? $flash->get('main_keywords' . $k) : @$seoDB['main_keywords' . $k] ?>">
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="seo_point" id="seo_point_<?=$k?>" >0</span>/100</div>
                                </div>
                            </div>
                            <p style='margin: 5px 0; color: #f00;'>Chèn từ khóa bạn muốn xếp hạng.</p>
                        </div>
                        <div class="form-group seo-panel-group mb-0" id="seo-general-<?=$k?>">
                            <ul>
                                <?php foreach($listCriteria as $key => $label){?>
                                <li key="<?=$key?>" class="seo-check-<?=$key?> test-fail">
                                    <span class="icon"><i class="fa-regular fa-xmark"></i></span>
                                    <span class="txt"><?=$label?></span>
                                </li>
                                <?php }?>
                            </ul>
                        </div>
                        <?php }?>

                    </div>
                <?php } ?>
                <div class="tab-pane fade" id="tabs-seonangcao" role="tabpanel" aria-labelledby="tabs-lang">
                    <div class="form-group mb-0">
                     <div class="row">
                     <div class="col-md-3 col-lg-2">
                     <div class="label-seo d-block">
                     <label><strong>Robots Meta:</strong></label>
                     </div>
                     </div>
                     <div class="col-md-9 col-lg-10">
                     <div class="label-seo d-block">
                     <label><b>Index meta</b></label>
                     </div>
                     <div class="mb-3">
                     <div class="form-check">
                     <input name="metaindex" class="form-check-input" type="radio" value="noindex" id="noindex" <?=($item['metaindex']=='noindex') ? 'checked' : '' ?> >
                     <label class="form-check-label" for="noindex"> No Index </label>
                     </div>
                     <div class="form-check">
                     <input name="metaindex" class="form-check-input" type="radio" value="index" id="index" <?=($item['metaindex']=='index' || $item['metaindex']=='') ? 'checked' : '' ?>>
                     <label class="form-check-label" for="index"> Index </label>
                     </div>
                     </div>
                     <?php $metaorder = ($item['metaorder']!='') ? explode(",", $item['metaorder']) : array(); ?>
                      <div class="label-seo d-block">
                     <label><b>Meta order</b></label>
                     <div>
                     <div class="form-check form-check-primary">
                     <input class="form-check-input" name="metaorder[]" <?php if(in_array('nofollow', $metaorder)) echo 'checked' ?> type="checkbox" value="nofollow" id="nofollow">
                     <label class="form-check-label" for="nofollow">No Follow</label>
                     </div>
                     <div class="form-check form-check-primary">
                     <input class="form-check-input" name="metaorder[]" <?php if(in_array('noarchive', $metaorder)) echo 'checked' ?> type="checkbox" value="noarchive" id="noarchive">
                     <label class="form-check-label" for="noarchive">No Archive</label>
                     </div>
                     <div class="form-check form-check-primary">
                     <input class="form-check-input" name="metaorder[]" <?php if(in_array('noimageindex', $metaorder)) echo 'checked' ?> type="checkbox" value="noimageindex" id="noimageindex">
                     <label class="form-check-label" for="noimageindex">No Image Index</label>
                     </div>
                     <div class="form-check form-check-primary">
                     <input class="form-check-input" name="metaorder[]" <?php if(in_array('nosnippet', $metaorder)) echo 'checked' ?> type="checkbox" value="nosnippet" id="nosnippet">
                     <label class="form-check-label" for="nosnippet">No Snippet</label>
                     </div>
                     </div>
                     </div>
                      </div>
                     </div>
                     </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="seo-create" value="<?= (isset($seo_create)) ? rtrim($seo_create, ",") : '' ?>">
    </div>
</div>

<script type="text/javascript">
function seoRankMath() {
    return {
        icon : {
            'error'   : '<i class="fa-regular fa-xmark"></i>',
            'success' : '<i class="fa-regular fa-check"></i>'
        },
        messageError : {
            'keywordInTitle' : 'Thêm Từ khóa chính vào tiêu đề SEO.'
        },
        messageSuccess : {
            'keywordInTitle' : 'Tuyệt vời! Bạn đang sử dụng Keyword chính trong tiêu đề SEO.',
            'lengthTitle' : 'Tuyệt vời! Tiêu đề của bạn đã có độ dài tối ưu',
            'lengthMetaDescription' : 'Tuyệt vời! Mô tả meta seo của bạn đã có độ dài tối ưu'
        },
        domain:'<?=rtrim($configBase,'/')?>',
        lang:'vi',
        content: {
            <?php foreach ($config['website']['seo'] as $k => $v) { ?>
            '<?=$k?>': '',
            <?php } ?>
        },
        get keyword(){
            return {
                <?php foreach ($config['website']['seo'] as $k => $v) { ?>
                '<?=$k?>':{'this'  : $('#main_keywords<?=$k?>'),'value': $('#main_keywords<?=$k?>').val()},
                <?php } ?>
            }
        },
        get title(){
            return {
                <?php foreach ($config['website']['seo'] as $k => $v) { ?>
                '<?=$k?>':{'this'  : $('#title<?=$k?>'),'value': $('#title<?=$k?>').val()},
                <?php } ?>
            };
        },
        get description(){
            return {
                <?php foreach ($config['website']['seo'] as $k => $v) { ?>
                '<?=$k?>': {'this': $('#description<?=$k?>'), 'value': $('#description<?=$k?>').val()},
                <?php } ?>
            }
        },
        get slug(){
            return {
                <?php foreach ($config['website']['seo'] as $k => $v) { ?>
                '<?=$k?>': $('#slug<?=$k?>').val(),
                <?php } ?>
            }
        },
        init() {
            var root = this;
            setTimeout(function (){
                for (var editorId in CKEDITOR.instances) {
                    let subID = editorId.substring(7);
                    root.content[subID] =  CKEDITOR.instances[editorId].getData();
                }
            },100);

            <?php foreach ($config['website']['seo'] as $k => $v) { ?>
            this.title[`<?=$k?>`].value = this.title[`<?=$k?>`].value.toLowerCase();
            this.keyword[`<?=$k?>`].value = this.keyword[`<?=$k?>`].value.toLowerCase();
            this.description[`<?=$k?>`].value = this.description[`<?=$k?>`].value.toLowerCase();
            let target<?=$k?> = document.getElementById('slugurlpreview<?=$k?>').firstElementChild;
            let observer<?=$k?> = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    root.slug['<?=$k?>'] = mutation.target.textContent.toLowerCase();
                });
            });
            observer<?=$k?>.observe(target<?=$k?>, { childList: true });
            <?php }?>
            setTimeout(function (){
                for (var editorId in CKEDITOR.instances) {
                    let subID = editorId.substring(7);
                    var editor = CKEDITOR.instances[editorId];
                    if (editor) {
                        editor.on('change', function(evt) {
                            var nnn = evt.editor.getData();
                            root.setContent(subID,nnn);
                            root.seoRankMathGroup();
                        }, this);
                    }
                }
            },100);
            setTimeout(function (){
                root.seoRankMathGroup();
            },200);
        },
        setContent(key,value){
            this.content[key] = value;
        },
        getContent(key){
            return this.content[key];
        },
        async seoRankMathGroup(){
            let point = 0;
            let root = this;
            let seoPanel = $(`#seo-general-`+this.lang);
            let regex = new RegExp(root.keyword[root.lang].value, "i");
            if(this.keyword[this.lang].value.length !== 0) {
                if(root.title[root.lang].value.search(regex) !== -1) {
                    point++;
                    root.seoRankMathChangeStatus(`keywordInTitle`, `success`);
                }else root.seoRankMathChangeStatus(`keywordInTitle`, `error`);
                let beginTitle = root.title[root.lang].value.search(regex);
                if(beginTitle === 0) {
                    point++;
                    root.seoRankMathChangeStatus(`titleStartWithKeyword`, `success`);
                }else if(beginTitle == -1) {
                    root.seoRankMathChangeStatus(`titleStartWithKeyword`, `error`);
                }else {
                    let endTitle = beginTitle + root.keyword[root.lang].value.length;
                    if(endTitle === root.title[root.lang].value.length) {
                        root.seoRankMathChangeStatus(`titleStartWithKeyword`, `error`);
                    }else {
                        point++;
                        root.seoRankMathChangeStatus(`titleStartWithKeyword`, `success`);
                    }
                }
            }
            let titleLength = root.title[root.lang].value.length;
            if(titleLength >= 10 && titleLength <= 70) {
                point++;
                root.seoRankMathChangeStatus('lengthTitle', 'success');
            }
            else {
                if(titleLength > 70) mess = 'Tiêu đề có '+ titleLength +' ký tự. Hãy xem xét rút ngắn nó.';
                if(titleLength < 10) mess = 'Tiêu đề '+ titleLength +' ký tự (ngắn). Cố gắng có được 70 ký tự'; seoPanel.find('li[key="lengthTitle"]').removeClass('test-success').addClass('test-fail'); seoPanel.find('li[key="lengthTitle"]').find('span.txt').html(mess); seoPanel.find('li[key="lengthTitle"]').find('span.icon').html(this.icon.error);
            }
            if(root.keyword[root.lang].value.length !== 0) {
                if (root.description[root.lang].value.search(regex) !== -1) {
                    point++;
                    root.seoRankMathChangeStatus('keywordInMetaDescription', 'success');
                }else root.seoRankMathChangeStatus('keywordInMetaDescription', 'error');
            }
            let descriptionLength = root.description[root.lang].value.length;
            if(descriptionLength >= 160 && descriptionLength <= 300) {
                point++;
                root.seoRankMathChangeStatus('lengthMetaDescription', 'success');
            }
            else {
                if(descriptionLength > 300) mess = 'Mô tả meta SEO có '+ descriptionLength +' ký tự. Hãy xem xét rút ngắn nó.';
                if(descriptionLength < 160) mess = 'Mô tả meta SEO có '+ descriptionLength +' ký tự (ngắn). Cố gắng thành 160 ký tự';
                seoPanel.find('li[key="lengthMetaDescription"]').removeClass('test-success').addClass('test-fail');
                seoPanel.find('li[key="lengthMetaDescription"]').find('span.txt').html(mess);
                seoPanel.find('li[key="lengthMetaDescription"]').find('span.icon').html(root.icon.error);
            }
            if(root.keyword[root.lang].value.length !== 0) {
                if (root.slug[root.lang].search(root.ChangeToSlug(root.keyword[root.lang].value)) !== -1) {
                    point++;
                    root.seoRankMathChangeStatus('keywordInPermalink', 'success');
                }
            }
            let object = seoPanel.find('li[key="lengthPermalink"]');
            let slugLength = root.slug[root.lang].length + root.domain.length - 8;
            if(slugLength > 75 || slugLength < 35) {
                if(slugLength > 75) mess = 'Url có '+ slugLength +' ký tự (dài). Hãy xem xét rút ngắn nó.';
                if(slugLength < 35) mess = 'Url có '+ slugLength +' ký tự (ngắn).';
                object.removeClass('test-success').addClass('test-fail');
                object.find('span.txt').html(mess);
                object.find('span.icon').html(root.icon.error);
            }
            else {
                point++;
                mess = 'Url có '+ slugLength +' ký tự. Tuyệt vời!';
                object.removeClass('test-fail').addClass('test-success');
                object.find('span.txt').html(mess);
                object.find('span.icon').html(root.icon.success);
            }
            let contentRemoveHtml = await root.stripHtml(root.content[root.lang]).toLowerCase();
            if(root.keyword[root.lang].value.length !== 0) {
                let regex = new RegExp(root.keyword[root.lang].value, "i");
                let searchKey = contentRemoveHtml.search(regex);
                
                if (searchKey !== -1) {
                    point++;
                    root.seoRankMathChangeStatus('keywordInContent', 'success');
                    let firstKeyword = contentRemoveHtml.substr(0,root.keyword[root.lang].value.length).toLowerCase();
                    console.log(firstKeyword);
                    console.log(root.keyword[root.lang].value.toLowerCase());
                    console.log(root.keyword[root.lang].value.toLowerCase().trimEnd() === firstKeyword.trimEnd());
                    if (root.keyword[root.lang].value.toLowerCase().trimEnd() === firstKeyword.trimEnd()) {
                        point++;
                        root.seoRankMathChangeStatus('keywordIn10Percent', 'success');
                    }
                }
            }
            let contentWord = contentRemoveHtml.split(/[\s.,;]+/).length;
            if(contentWord >= 600 && contentWord <= 2500) {
                point++;
                root.seoRankMathChangeStatus('lengthContent', 'success');
            }
            else root.seoRankMathChangeStatus('lengthContent', 'error');
            let tmp = document.createElement('div');
            tmp.innerHTML = root.content[root.lang];
            let internalLinks = tmp.getElementsByTagName("a");
            if (internalLinks.length === 0) root.seoRankMathChangeStatus('linksHasInternal', 'error');
            else {
                let linksHasInternal = false;
                $.each(internalLinks, function (index, value) {
                    if (internalLinks[index].href.toLowerCase().search(root.domain) !== -1) {
                        point++;
                        root.seoRankMathChangeStatus('linksHasInternal', 'success');
                        linksHasInternal = true;
                        return true;
                    }
                });
                if (linksHasInternal === false) root.seoRankMathChangeStatus('linksHasInternal', 'error');
            }
            if (root.keyword[root.lang].value.length !== 0) {
                let regex = new RegExp(root.keyword[root.lang].value, "i");
                let keywordInSubheadings = false;
                let headingH2 = tmp.getElementsByTagName('h2');
                if (headingH2.length !== 0) {
                    $.each(headingH2, function (index, value) {

                        if (headingH2[index].innerText.toLowerCase().search(regex) !== -1) {
                            point++;
                            root.seoRankMathChangeStatus('keywordInSubheadings', 'success');
                            keywordInSubheadings = true;
                            return true;
                        }
                    });
                }
                let headingH3 = tmp.getElementsByTagName('h3');
                if (keywordInSubheadings === false && headingH3.length !== 0) {
                    $.each(headingH3, function (index, value) {
                        if (headingH3[index].innerText.toLowerCase().search(regex) !== -1) {
                            point++;
                            root.seoRankMathChangeStatus('keywordInSubheadings', 'success');
                            keywordInSubheadings = true;
                            return true;
                        }
                    });
                }
                let headingH4 = tmp.getElementsByTagName('h4');
                if (keywordInSubheadings === false && headingH4.length !== 0) {
                    $.each(headingH4, function (index, value) {
                        if (headingH4[index].innerText.toLowerCase().search(regex) !== -1) {
                            point++;
                            root.seoRankMathChangeStatus('keywordInSubheadings', 'success');
                            keywordInSubheadings = true;
                            return true;
                        }
                    });
                }
                let headingH5 = tmp.getElementsByTagName('h5');
                if (keywordInSubheadings === false && headingH5.length !== 0) {
                    $.each(headingH5, function (index, value) {
                        if (headingH5[index].innerText.toLowerCase().search(regex) !== -1) {
                            point++;
                            root.seoRankMathChangeStatus('keywordInSubheadings', 'success');
                            keywordInSubheadings = true;
                            return true;
                        }
                    });
                }
                let headingH6 = tmp.getElementsByTagName('h5');
                if (keywordInSubheadings === false && headingH6.length !== 0) {
                    $.each(headingH6, function (index, value) {
                        if (headingH6[index].innerText.toLowerCase().search(regex) !== -1) {
                            point++;
                            root.seoRankMathChangeStatus('keywordInSubheadings', 'success');
                            keywordInSubheadings = true;
                            return true;
                        }
                    });
                }
                if(keywordInSubheadings === false) root.seoRankMathChangeStatus('keywordInSubheadings', 'error');
            }
            let img = tmp.getElementsByTagName('img');
            if (img.length === 0) {
                root.seoRankMathChangeStatus('keywordInImageAlt', 'error');
                root.seoRankMathChangeStatus('contentHasAssets', 'error');
            } else {
                if(root.keyword[root.lang].value.length !== 0) {
                    let keywordInImageAlt = false;
                    if (img.length >= 2) {
                        point++;
                        root.seoRankMathChangeStatus('contentHasAssets', 'success');
                    } else root.seoRankMathChangeStatus('contentHasAssets', 'error');
                    $.each(img, function (index, value) {
                        let regex = new RegExp(root.keyword[root.lang].value, "i");
                        if (img[index].alt.toLowerCase().search(regex) !== -1) {
                            point++;
                            root.seoRankMathChangeStatus('keywordInImageAlt', 'success');
                            keywordInImageAlt = true;
                            return true;
                        }
                    });
                    if (keywordInImageAlt === false) root.seoRankMathChangeStatus('keywordInImageAlt', 'error');
                }
            }
            if (root.keyword[root.lang].value.length !== 0) {
                object = seoPanel.find('li[key="keywordDensity"]');
                let mess;
                let contentRemoveHtml = await root.stripHtml(root.content[root.lang]).toLowerCase();
                let contentWord = contentRemoveHtml.split(/[\s.,;]+/).length;
                let nkr = root.occurrences(contentRemoveHtml, root.keyword[root.lang].value.toLowerCase());
                let keywordDensity = (nkr / contentWord) * 100;
                keywordDensity = keywordDensity.toFixed(2);
                if(keywordDensity > 2.5 || keywordDensity < 0.75) {
                    if(keywordDensity > 2.5) mess = 'Mật độ từ khóa là '+ keywordDensity +' (cao). Số lần từ khóa xuất hiện là ' +nkr+'.';
                    if(keywordDensity < 0.75) mess = 'Mật độ từ khóa là '+ keywordDensity +' (thấp). Số lần từ khóa xuất hiện là ' +nkr+'.';
                    object.removeClass('test-success').addClass('test-fail');
                    object.find('span.txt').html(mess);
                    object.find('span.icon').html(root.icon.error);
                }
                else {
                    point++;
                    mess = 'Mật độ từ khóa là '+ keywordDensity +'. Số lần từ khóa xuất hiện là ' +nkr+'.';
                    object.removeClass('test-fail').addClass('test-success');
                    object.find('span.txt').html(mess);
                    object.find('span.icon').html(root.icon.success);
                }
            }
            let tagP = tmp.getElementsByTagName('p');
            if (tagP.length >= 2) {
                point++;
                root.seoRankMathChangeStatus('contentHasShortParagraphs', 'success');
            } else root.seoRankMathChangeStatus('contentHasShortParagraphs', 'error');
            if(root.keyword[root.lang].value.length === 0) {
                root.seoRankMathChangeStatus('keywordNotUsed', 'error');
                root.seoRankMathChangeStatus('keywordInTitle', 'error');
                root.seoRankMathChangeStatus('titleStartWithKeyword', 'error');
                root.seoRankMathChangeStatus('keywordInMetaDescription', 'error');
                root.seoRankMathChangeStatus('keywordInPermalink', 'error');
                root.seoRankMathChangeStatus('keywordInContent', 'error');
                root.seoRankMathChangeStatus('keywordIn10Percent', 'error');
                root.seoRankMathChangeStatus('keywordInImageAlt', 'error');
                root.seoRankMathChangeStatus('keywordDensity', 'error');
                root.seoRankMathChangeStatus('keywordInSubheadings', 'error');
            }
            else {
                point++;
                root.seoRankMathChangeStatus('keywordNotUsed', 'success');
            }
            point = ((point>17?17:point)/17)*100;
            $('#seo_point_'+root.lang).html(Math.ceil(point));
        },
        seoRankMathChangeStatus(key, status){
            let object = $(`#seo-general-`+this.lang).find('li[key="'+key+'"]');
            if(status === 'success') {
                object.removeClass('test-fail').addClass('test-success');
                object.find('span.txt').html(this.messageSuccess[key]);
                object.find('span.icon').html(this.icon.success);
            } else {
                object.removeClass('test-success').addClass('test-fail');
                object.find('span.txt').html(this.messageError[key]);
                object.find('span.icon').html(this.icon.error);
            }
        },
        stripHtml(html) {
            let tmp = document.createElement("DIV");
            tmp.innerHTML = html;
            return tmp.textContent || tmp.innerText || "";
        },
        ChangeToSlug(title) {
            let slug = title.toLowerCase();
            slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
            slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
            slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
            slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
            slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
            slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
            slug = slug.replace(/đ/gi, 'd');
            slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\:|\;|_/gi, '');
            slug = slug.replace(/ /gi, "-");
            slug = slug.replace(/\-\-\-\-\-/gi, '-');
            slug = slug.replace(/\-\-\-\-/gi, '-');
            slug = slug.replace(/\-\-\-/gi, '-');
            slug = slug.replace(/\-\-/gi, '-');
            slug = '@' + slug + '@';
            slug = slug.replace(/\@\-|\-\@|\@/gi, '');
            return slug;
        },
        occurrences(string, subString, allowOverlapping) {
            string += "";
            subString += "";
            if (subString.length <= 0) return (string.length + 1);
            var n = 0,
                pos = 0,
                step = allowOverlapping ? 1 : subString.length;
            while (true) {
                pos = string.indexOf(subString, pos);
                if (pos >= 0) {
                    ++n;
                    pos += step;
                } else break;
            }
            return n;
        }
    };
}
</script>
