<?php

if (!defined('ABSPATH')) {
    exit;
}

class GWOB_Shortcodes
{
    private const ALLOWED_FLIPBOOK_SHORTCODES = ['fptf-flipbook'];

    public function register(): void
    {
        add_shortcode('gw_bookshelf', [$this, 'bookshelf']);
        add_shortcode('gw_upload_guide', [$this, 'upload_guide']);
        add_shortcode('gw_book_list', [$this, 'book_list']);
        add_shortcode('gw_featured_books', [$this, 'featured_books']);
    }

    public function bookshelf($atts = []): string
    {
        $atts = is_array($atts) ? $atts : [];
        $atts = shortcode_atts([
            'category' => '',
            'columns' => 3,
            'limit' => 12,
            'show_search' => '1',
            'show_category_filter' => '1',
            'featured' => '',
        ], $atts, 'gw_bookshelf');

        $query = $this->book_query($atts);
        $terms = get_terms(['taxonomy' => GWOB_Post_Type::TAXONOMY, 'hide_empty' => true]);

        ob_start();
        include GWOB_PLUGIN_DIR . 'templates/bookshelf.php';
        wp_reset_postdata();

        return (string) ob_get_clean();
    }

    public function upload_guide(): string
    {
        ob_start();
        include GWOB_PLUGIN_DIR . 'templates/upload-guide.php';
        return (string) ob_get_clean();
    }

    public function book_list($atts = []): string
    {
        $atts = is_array($atts) ? $atts : [];
        $atts = shortcode_atts(['limit' => 20], $atts, 'gw_book_list');
        $query = $this->book_query(['limit' => $atts['limit'], 'columns' => 1]);

        ob_start();
        echo '<ul class="gwob-book-list">';
        while ($query->have_posts()) {
            $query->the_post();
            if (GWOB_Access_Control::can_view(get_the_ID())) {
                echo '<li><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></li>';
            }
        }
        echo '</ul>';
        wp_reset_postdata();

        return (string) ob_get_clean();
    }

    public function featured_books($atts = []): string
    {
        $atts = is_array($atts) ? $atts : [];
        $atts['featured'] = '1';
        return $this->bookshelf($atts);
    }

    public static function render_flipbook_shortcode(int $post_id): string
    {
        $shortcode = trim((string) get_post_meta($post_id, '_gwob_flipbook_shortcode', true));

        if ($shortcode === '') {
            return self::render_pdf_fallback($post_id);
        }

        if (!self::is_allowed_shortcode($shortcode)) {
            return '<p class="gwob-empty">許可されていないショートコードのため表示できません。</p>';
        }

        return do_shortcode($shortcode);
    }

    private static function render_pdf_fallback(int $post_id): string
    {
        $pdf_url = get_post_meta($post_id, '_gwob_pdf_url', true);

        if (!$pdf_url) {
            return '<p class="gwob-empty">Flipbook ショートコードまたは PDF URL が未設定です。</p>';
        }

        return sprintf(
            '<iframe class="gwob-pdf-viewer" src="%s" title="%s"></iframe><p class="gwob-pdf-link"><a href="%s" target="_blank" rel="noopener">PDFを別タブで開く</a></p>',
            esc_url($pdf_url),
            esc_attr(get_the_title($post_id)),
            esc_url($pdf_url)
        );
    }

    private static function is_allowed_shortcode(string $shortcode): bool
    {
        if (!preg_match('/^\[([a-zA-Z0-9_-]+)/', $shortcode, $matches)) {
            return false;
        }

        return in_array($matches[1], self::ALLOWED_FLIPBOOK_SHORTCODES, true);
    }

    private function book_query(array $atts): WP_Query
    {
        $meta_query = [
            'relation' => 'OR',
            'gwob_order' => [
                'key' => '_gwob_order',
                'compare' => 'EXISTS',
                'type' => 'NUMERIC',
            ],
            'gwob_order_missing' => [
                'key' => '_gwob_order',
                'compare' => 'NOT EXISTS',
            ],
        ];

        $args = [
            'post_type' => GWOB_Post_Type::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => max(1, absint($atts['limit'] ?? 12)),
            'meta_query' => $meta_query,
            'orderby' => ['gwob_order' => 'ASC', 'date' => 'DESC'],
        ];

        if (!empty($atts['category'])) {
            $args['tax_query'] = [[
                'taxonomy' => GWOB_Post_Type::TAXONOMY,
                'field' => 'slug',
                'terms' => sanitize_title($atts['category']),
            ]];
        }

        if (!empty($atts['featured'])) {
            $args['meta_query'] = [
                'relation' => 'AND',
                $meta_query,
                ['key' => '_gwob_featured', 'value' => '1'],
            ];
        }

        return new WP_Query($args);
    }
}
