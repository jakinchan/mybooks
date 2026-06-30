<?php
/**
 * 汎用テキストページ（運営会社／プライバシーポリシー／利用規約など）。
 * 投稿本文を mybooks デザインで表示する。管理画面の本文編集がそのまま反映される。
 */
if (!defined('ABSPATH')) {
    exit;
}

the_post();
$title = get_the_title();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('mb-landing mb-doc'); ?>>
<?php wp_body_open(); ?>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-header.php'; ?>

<main class="mb-main">
    <section class="mb-page-hero">
        <div class="mb-container">
            <h1 class="mb-page-hero-title"><?php echo esc_html($title); ?></h1>
        </div>
    </section>

    <section class="mb-section">
        <div class="mb-container mb-doc-narrow">
            <article class="mb-doc-body">
                <?php the_content(); ?>
            </article>
        </div>
    </section>
</main>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-footer.php'; ?>

<?php wp_footer(); ?>
</body>
</html>
