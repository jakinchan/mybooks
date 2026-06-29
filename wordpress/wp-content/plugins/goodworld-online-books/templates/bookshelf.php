<?php
if (!defined('ABSPATH')) {
    exit;
}

$columns = min(4, max(1, absint($atts['columns'])));
?>
<section class="gwob-bookshelf" style="--gwob-columns: <?php echo esc_attr((string) $columns); ?>">
    <?php if ($atts['show_search'] === '1' || $atts['show_category_filter'] === '1') : ?>
        <div class="gwob-toolbar">
            <?php if ($atts['show_search'] === '1') : ?>
                <input class="gwob-search" type="search" placeholder="本を検索" aria-label="本を検索">
            <?php endif; ?>
            <?php if ($atts['show_category_filter'] === '1' && !is_wp_error($terms) && $terms) : ?>
                <select class="gwob-category-filter" aria-label="カテゴリーで絞り込み">
                    <option value="">すべてのカテゴリー</option>
                    <?php foreach ($terms as $term) : ?>
                        <option value="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="gwob-grid">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
            <?php
            if (!GWOB_Access_Control::can_view(get_the_ID())) {
                continue;
            }
            include GWOB_PLUGIN_DIR . 'templates/book-card.php';
            ?>
        <?php endwhile; ?>
    </div>
</section>
