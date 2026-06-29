<?php
if (!defined('ABSPATH')) {
    exit;
}

the_post();

$post_id = get_the_ID();
$subtitle = get_post_meta($post_id, '_gwob_subtitle', true);
$author = get_post_meta($post_id, '_gwob_author', true);
$page_count = get_post_meta($post_id, '_gwob_page_count', true);
$terms = get_the_terms($post_id, GWOB_Post_Type::TAXONOMY);
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
<main class="gwob-single-wrap">
    <article class="gwob-single">
        <a class="gwob-site-name" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
        <a class="gwob-back" href="<?php echo esc_url(home_url('/books/')); ?>">本棚へ戻る</a>
        <header class="gwob-single-header">
            <div>
                <p class="gwob-kicker">Now Reading</p>
                <h1><?php the_title(); ?></h1>
                <?php if ($subtitle) : ?><p class="gwob-single-subtitle"><?php echo esc_html($subtitle); ?></p><?php endif; ?>
                <div class="gwob-meta">
                    <?php if ($author) : ?><span><?php echo esc_html($author); ?></span><?php endif; ?>
                    <?php if ($page_count) : ?><span><?php echo esc_html($page_count); ?>ページ</span><?php endif; ?>
                </div>
                <?php if (is_array($terms)) : ?>
                    <div class="gwob-categories">
                        <?php foreach ($terms as $term) : ?><span><?php echo esc_html($term->name); ?></span><?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <aside class="gwob-single-book" aria-hidden="true">
                <div class="gwob-cover gwob-cover-static">
                    <span class="gwob-book-spine"></span>
                    <span class="gwob-book-pages"></span>
                    <span class="gwob-cover-face">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large'); ?>
                        <?php else : ?>
                            <span class="gwob-cover-title"><?php the_title(); ?></span>
                            <?php if ($subtitle) : ?><span class="gwob-cover-subtitle"><?php echo esc_html($subtitle); ?></span><?php endif; ?>
                            <span class="gwob-cover-mark">PDF</span>
                        <?php endif; ?>
                    </span>
                </div>
            </aside>
        </header>

        <div class="gwob-description"><?php the_content(); ?></div>

        <section class="gwob-reading-desk" aria-label="PDF viewer">
            <div class="gwob-page-shadow" aria-hidden="true"></div>
            <div class="gwob-page-spread">
                <div class="gwob-page-gutter" aria-hidden="true"></div>
                <div class="gwob-flipbook">
                    <?php
                    if (GWOB_Access_Control::can_view($post_id)) {
                        echo GWOB_Shortcodes::render_flipbook_shortcode($post_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    } else {
                        echo GWOB_Access_Control::denied_message(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    }
                    ?>
                </div>
            </div>
        </section>
    </article>
</main>
<?php wp_footer(); ?>
</body>
</html>
