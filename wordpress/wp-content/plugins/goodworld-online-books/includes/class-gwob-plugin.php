<?php

if (!defined('ABSPATH')) {
    exit;
}

class GWOB_Plugin
{
    public function run(): void
    {
        add_action('plugins_loaded', [$this, 'load_textdomain']);

        (new GWOB_Dependency_Checker())->register();
        (new GWOB_Post_Type())->register();
        (new GWOB_Admin())->register();
        (new GWOB_Assets())->register();
        (new GWOB_Template_Loader())->register();
        (new GWOB_Shortcodes())->register();
    }

    public function load_textdomain(): void
    {
        load_plugin_textdomain('goodworld-online-books', false, dirname(plugin_basename(GWOB_PLUGIN_FILE)) . '/languages');
    }

    public static function activate(): void
    {
        require_once GWOB_PLUGIN_DIR . 'includes/class-gwob-post-type.php';
        (new GWOB_Post_Type())->register_types();
        self::create_default_pages();
        flush_rewrite_rules();
    }

    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }

    private static function create_default_pages(): void
    {
        $pages = [
            ['title' => 'オンライン本棚', 'slug' => 'books', 'content' => '[gw_bookshelf]'],
            ['title' => 'PDF本の作り方', 'slug' => 'upload-book-guide', 'content' => '[gw_upload_guide]'],
        ];

        foreach ($pages as $page) {
            if (get_page_by_path($page['slug'])) {
                continue;
            }

            wp_insert_post([
                'post_title' => $page['title'],
                'post_name' => $page['slug'],
                'post_content' => $page['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
            ]);
        }
    }
}
