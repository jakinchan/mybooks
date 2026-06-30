<?php
/**
 * mybooks 共通ヘッダー（ランディング／ガイド等の独立テンプレートで使用）。
 * 自己完結。include するだけで使える。
 */
if (!defined('ABSPATH')) {
    exit;
}

$mb_home_url  = home_url('/');
$mb_books_url = home_url('/books/');
$mb_guide_url = home_url('/upload-book-guide/');

$mb_demo_url = $mb_books_url;
$mb_sample   = get_page_by_path('sample-goodworld-online-book', OBJECT, GWOB_Post_Type::POST_TYPE);
if ($mb_sample instanceof WP_Post) {
    $mb_demo_url = get_permalink($mb_sample);
}

$mb_nav_links = [
    ['label' => 'PDF本の作り方', 'url' => $mb_guide_url],
    ['label' => 'オンライン本棚', 'url' => $mb_books_url],
    ['label' => 'サンプルページ', 'url' => $mb_demo_url],
    ['label' => '料金', 'url' => home_url('/pricing/')],
    ['label' => 'お問い合わせ', 'url' => home_url('/contact/')],
];
?>
<header class="mb-header" data-mb-header>
    <div class="mb-container mb-header-inner">
        <a class="mb-logo" href="<?php echo esc_url($mb_home_url); ?>"><?php bloginfo('name'); ?></a>
        <nav class="mb-nav" aria-label="メインナビゲーション">
            <?php foreach ($mb_nav_links as $mb_link) : ?>
                <a href="<?php echo esc_url($mb_link['url']); ?>"><?php echo esc_html($mb_link['label']); ?></a>
            <?php endforeach; ?>
        </nav>
        <a class="mb-btn mb-btn-primary mb-header-cta" href="<?php echo esc_url($mb_guide_url); ?>">無料で始める</a>
        <button type="button" class="mb-nav-toggle" data-mb-nav-toggle aria-label="メニューを開く" aria-expanded="false" aria-controls="mb-mobile-nav">
            <span class="mb-nav-toggle-bar"></span>
            <span class="mb-nav-toggle-bar"></span>
            <span class="mb-nav-toggle-bar"></span>
        </button>
    </div>

    <nav class="mb-mobile-nav" id="mb-mobile-nav" data-mb-mobile-nav aria-label="モバイルナビゲーション" hidden>
        <?php foreach ($mb_nav_links as $mb_link) : ?>
            <a href="<?php echo esc_url($mb_link['url']); ?>"><?php echo esc_html($mb_link['label']); ?></a>
        <?php endforeach; ?>
        <a class="mb-btn mb-btn-primary mb-mobile-cta" href="<?php echo esc_url($mb_guide_url); ?>">無料で始める</a>
    </nav>
</header>
