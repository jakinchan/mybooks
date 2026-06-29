<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
the_post();

$post_id = get_the_ID();
$subtitle = get_post_meta($post_id, '_gwob_subtitle', true);
$author = get_post_meta($post_id, '_gwob_author', true);
$page_count = get_post_meta($post_id, '_gwob_page_count', true);
$terms = get_the_terms($post_id, GWOB_Post_Type::TAXONOMY);
?>
<main class="gwob-single-wrap">
    <article class="gwob-single">
        <a class="gwob-back" href="<?php echo esc_url(home_url('/books/')); ?>">本棚へ戻る</a>
        <header class="gwob-single-header">
            <div>
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
            <?php if (has_post_thumbnail()) : ?><div class="gwob-single-cover"><?php the_post_thumbnail('large'); ?></div><?php endif; ?>
        </header>

        <div class="gwob-description"><?php the_content(); ?></div>

        <section class="gwob-flipbook">
            <?php
            if (GWOB_Access_Control::can_view($post_id)) {
                echo GWOB_Shortcodes::render_flipbook_shortcode($post_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            } else {
                echo GWOB_Access_Control::denied_message(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
            ?>
        </section>
    </article>
</main>
<?php get_footer(); ?>
