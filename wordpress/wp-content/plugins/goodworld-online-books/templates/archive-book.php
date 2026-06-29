<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main class="gwob-archive-wrap">
    <header class="gwob-archive-header">
        <h1>オンライン本棚</h1>
        <p>教材、マニュアル、カタログ、社内資料をオンライン本として閲覧できます。</p>
    </header>
    <?php echo do_shortcode('[gw_bookshelf]'); ?>
</main>
<?php get_footer(); ?>
