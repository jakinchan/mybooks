<?php

if (!defined('ABSPATH')) {
    exit;
}

class GWOB_Assets
{
    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'frontend']);
        add_action('admin_enqueue_scripts', [$this, 'admin']);
    }

    public function frontend(): void
    {
        wp_enqueue_style('gwob-frontend', GWOB_PLUGIN_URL . 'assets/css/frontend.css', [], GWOB_VERSION);
        wp_enqueue_script('gwob-frontend', GWOB_PLUGIN_URL . 'assets/js/frontend.js', [], GWOB_VERSION, true);
    }

    public function admin(string $hook): void
    {
        global $post_type;

        if ($post_type !== GWOB_Post_Type::POST_TYPE) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('gwob-admin', GWOB_PLUGIN_URL . 'assets/css/admin.css', [], GWOB_VERSION);
        wp_enqueue_script('gwob-admin', GWOB_PLUGIN_URL . 'assets/js/admin.js', ['jquery'], GWOB_VERSION, true);
    }
}
