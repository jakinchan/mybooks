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
<body <?php body_class('gwob-document'); ?>>
<?php wp_body_open(); ?>
<main class="gwob-archive-wrap">
    <a class="gwob-site-name" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
    <header class="gwob-archive-header">
        <p class="gwob-kicker">Digital Library</p>
        <h1>オンライン本棚</h1>
        <p>教材、マニュアル、カタログ、社内資料を、紙の本を手に取るような感覚で閲覧できます。</p>
    </header>
    <?php echo do_shortcode('[gw_bookshelf]'); ?>
</main>
<?php wp_footer(); ?>
</body>
</html>
