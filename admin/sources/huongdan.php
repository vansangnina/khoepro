<?php
/**
 * Controller: Hướng Dẫn Sử Dụng Admin KhoePro
 * Sources: admin/sources/huongdan.php
 * PHP 7.4+ Compatible
 */

if (!defined('SOURCES')) die("Error");

require_once __DIR__ . '/../../huongdan/data/guide_data.php';

$act = htmlspecialchars($act ?? 'man');
$id = htmlspecialchars($_GET['id'] ?? '');
$category = htmlspecialchars($_GET['category'] ?? '');
$keyword = htmlspecialchars($_GET['keyword'] ?? '');

switch ($act) {
    case "detail":
        $article = GuideRepository::getArticle($id);
        if (empty($article)) {
            $func->transfer("Bài hướng dẫn không tồn tại hoặc đã bị di chuyển.", "index.php?com=huongdan&act=man", false);
        }
        $categoryInfo = GuideRepository::getCategories()[$article['category_id']] ?? null;
        $relatedArticles = array();
        if (!empty($article['related_links'])) {
            foreach ($article['related_links'] as $rId) {
                $rArt = GuideRepository::getArticle($rId);
                if ($rArt) $relatedArticles[] = $rArt;
            }
        }
        $template = "huongdan/man/detail";
        break;

    case "man":
    default:
        $categories = GuideRepository::getCategories();
        if (!empty($keyword)) {
            $articles = GuideRepository::searchArticles($keyword);
            $selectedCategory = null;
        } elseif (!empty($category)) {
            $articles = GuideRepository::getArticlesByCategory($category);
            $selectedCategory = $categories[$category] ?? null;
        } else {
            $articles = GuideRepository::getArticles();
            $selectedCategory = null;
        }
        $template = "huongdan/man/items";
        break;
}
