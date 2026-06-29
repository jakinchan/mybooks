<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID();
$subtitle = get_post_meta($post_id, '_gwob_subtitle', true);
$author = get_post_meta($post_id, '_gwob_author', true);
$page_count = get_post_meta($post_id, '_gwob_page_count', true);
$featured = get_post_meta($post_id, '_gwob_featured', true);
$terms = get_the_terms($post_id, GWOB_Post_Type::TAXONOMY);
$term_slugs = is_array($terms) ? implode(' ', wp_list_pluck($terms, 'slug')) : '';
?>
<article class="gwob-card" data-title="<?php echo esc_attr(strtolower(get_the_title() . ' ' . $subtitle . ' ' . $author)); ?>" data-categories="<?php echo esc_attr($term_slugs); ?>">
    <a class="gwob-cover" href="<?php the_permalink(); ?>">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('medium_large'); ?>
        <?php else : ?>
            <span class="gwob-cover-placeholder">PDF</span>
        <?php endif; ?>
        <?php if ($featured) : ?>
            <span class="gwob-badge">おすすめ</span>
        <?php endif; ?>
    </a>
    <div class="gwob-card-body">
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <?php if ($subtitle) : ?><p class="gwob-subtitle"><?php echo esc_html($subtitle); ?></p><?php endif; ?>
        <div class="gwob-meta">
            <?php if ($author) : ?><span><?php echo esc_html($author); ?></span><?php endif; ?>
            <?php if ($page_count) : ?><span><?php echo esc_html($page_count); ?>ページ</span><?php endif; ?>
        </div>
        <?php if (is_array($terms)) : ?>
            <div class="gwob-categories">
                <?php foreach ($terms as $term) : ?><span><?php echo esc_html($term->name); ?></span><?php endforeach; ?>
            </div>
        <?php endif; ?>
        <a class="gwob-read-button" href="<?php the_permalink(); ?>">本を読む</a>
    </div>
</article>
