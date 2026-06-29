<?php

if (!defined('ABSPATH')) {
    exit;
}

class GWOB_Post_Type
{
    public const POST_TYPE = 'gw_book';
    public const TAXONOMY = 'gw_book_category';

    public function register(): void
    {
        add_action('init', [$this, 'register_types']);
        add_action('init', [$this, 'register_default_terms'], 20);
    }

    public function register_types(): void
    {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name' => 'オンライン本',
                'singular_name' => 'オンライン本',
                'add_new_item' => 'オンライン本を追加',
                'edit_item' => 'オンライン本を編集',
                'menu_name' => 'オンライン本',
            ],
            'public' => true,
            'has_archive' => false,
            'rewrite' => ['slug' => 'books'],
            'menu_icon' => 'dashicons-book',
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'show_in_rest' => true,
        ]);

        register_taxonomy(self::TAXONOMY, [self::POST_TYPE], [
            'labels' => [
                'name' => '本カテゴリー',
                'singular_name' => '本カテゴリー',
                'menu_name' => '本カテゴリー',
            ],
            'hierarchical' => true,
            'public' => true,
            'rewrite' => ['slug' => 'book-category'],
            'show_in_rest' => true,
        ]);
    }

    public function register_default_terms(): void
    {
        foreach (['教材', 'マニュアル', 'カタログ', '社内資料', '会議', 'AI', '開発手順書'] as $term) {
            if (!term_exists($term, self::TAXONOMY)) {
                wp_insert_term($term, self::TAXONOMY);
            }
        }
    }
}
