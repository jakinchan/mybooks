<?php

if (!defined('ABSPATH')) {
    exit;
}

class GWOB_Admin
{
    private const NONCE_ACTION = 'gwob_save_book_meta';
    private const NONCE_NAME = 'gwob_book_meta_nonce';

    public function register(): void
    {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . GWOB_Post_Type::POST_TYPE, [$this, 'save_meta']);
        add_filter('manage_' . GWOB_Post_Type::POST_TYPE . '_posts_columns', [$this, 'columns']);
        add_action('manage_' . GWOB_Post_Type::POST_TYPE . '_posts_custom_column', [$this, 'column_content'], 10, 2);
    }

    public function add_meta_boxes(): void
    {
        add_meta_box('gwob_book_settings', 'オンライン本設定', [$this, 'render_meta_box'], GWOB_Post_Type::POST_TYPE, 'normal', 'high');
    }

    public function render_meta_box(WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
        $values = $this->meta_values($post->ID);
        include GWOB_PLUGIN_DIR . 'templates/admin-meta-box.php';
    }

    public function save_meta(int $post_id): void
    {
        if (!isset($_POST[self::NONCE_NAME]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[self::NONCE_NAME])), self::NONCE_ACTION)) {
            return;
        }

        if (!current_user_can('edit_post', $post_id) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
            return;
        }

        $schema = [
            '_gwob_pdf_url' => 'url',
            '_gwob_flipbook_shortcode' => 'shortcode',
            '_gwob_subtitle' => 'text',
            '_gwob_author' => 'text',
            '_gwob_page_count' => 'int',
            '_gwob_access_level' => 'access',
            '_gwob_order' => 'int',
            '_gwob_featured' => 'bool',
            '_gwob_allow_public_share' => 'bool',
        ];

        foreach ($schema as $key => $type) {
            $raw = $_POST[$key] ?? '';
            $value = is_string($raw) ? wp_unslash($raw) : $raw;
            update_post_meta($post_id, $key, $this->sanitize($value, $type));
        }
    }

    public function columns(array $columns): array
    {
        $columns['gwob_author'] = '著者';
        $columns['gwob_access'] = '公開範囲';
        $columns['gwob_featured'] = 'おすすめ';

        return $columns;
    }

    public function column_content(string $column, int $post_id): void
    {
        if ($column === 'gwob_author') {
            echo esc_html(get_post_meta($post_id, '_gwob_author', true));
        } elseif ($column === 'gwob_access') {
            echo esc_html(get_post_meta($post_id, '_gwob_access_level', true) ?: 'public');
        } elseif ($column === 'gwob_featured') {
            echo get_post_meta($post_id, '_gwob_featured', true) ? 'はい' : 'いいえ';
        }
    }

    private function meta_values(int $post_id): array
    {
        return [
            '_gwob_pdf_url' => get_post_meta($post_id, '_gwob_pdf_url', true),
            '_gwob_flipbook_shortcode' => get_post_meta($post_id, '_gwob_flipbook_shortcode', true),
            '_gwob_subtitle' => get_post_meta($post_id, '_gwob_subtitle', true),
            '_gwob_author' => get_post_meta($post_id, '_gwob_author', true),
            '_gwob_page_count' => get_post_meta($post_id, '_gwob_page_count', true),
            '_gwob_access_level' => get_post_meta($post_id, '_gwob_access_level', true) ?: 'public',
            '_gwob_order' => get_post_meta($post_id, '_gwob_order', true),
            '_gwob_featured' => (bool) get_post_meta($post_id, '_gwob_featured', true),
            '_gwob_allow_public_share' => (bool) get_post_meta($post_id, '_gwob_allow_public_share', true),
        ];
    }

    private function sanitize($value, string $type)
    {
        if ($type === 'url') {
            return esc_url_raw((string) $value);
        }

        if ($type === 'int') {
            return max(0, absint($value));
        }

        if ($type === 'bool') {
            return $value ? '1' : '';
        }

        if ($type === 'access') {
            return in_array($value, ['public', 'logged_in', 'private'], true) ? $value : 'public';
        }

        return sanitize_text_field((string) $value);
    }
}
