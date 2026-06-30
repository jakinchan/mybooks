<?php
if (!defined('ABSPATH')) {
    exit;
}

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('gwob-document mb-landing'); ?>>
<?php wp_body_open(); ?>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-header.php'; ?>

<main class="gwob-archive-wrap">
    <nav class="mb-breadcrumb" aria-label="パンくずリスト">
        <a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a>
        <span class="mb-breadcrumb-sep" aria-hidden="true">/</span>
        <span aria-current="page">オンライン本棚</span>
    </nav>
    <header class="gwob-archive-header">
        <p class="gwob-kicker">Digital Library</p>
        <h1>オンライン本棚</h1>
        <p>教材、マニュアル、カタログ、社内資料を、紙の本を手に取るような感覚で閲覧できます。</p>
    </header>
    <?php echo do_shortcode('[gw_bookshelf]'); ?>

    <p class="mb-back-home">
        <a class="mb-btn mb-btn-ghost" href="<?php echo esc_url(home_url('/')); ?>">← トップページへ戻る</a>
    </p>
</main>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-footer.php'; ?>

<?php wp_footer(); ?>
</body>
</html>
