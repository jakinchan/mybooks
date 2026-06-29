<?php

if (!defined('ABSPATH')) {
    exit;
}

class GWOB_Dependency_Checker
{
    public function register(): void
    {
        add_action('admin_notices', [$this, 'render_admin_notices']);
    }

    public function render_admin_notices(): void
    {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        foreach ($this->missing_dependencies() as $message) {
            echo '<div class="notice notice-warning"><p>' . esc_html($message) . '</p></div>';
        }
    }

    private function missing_dependencies(): array
    {
        $messages = [];

        if (!$this->is_sqlite_integration_active()) {
            $messages[] = 'GoodWorld Online Books を利用するには SQLite Database Integration プラグインを有効化してください。';
        }

        if (!$this->is_flipbook_active()) {
            $messages[] = 'GoodWorld Online Books を利用するには Free PDF to Flipbook プラグインを有効化してください。';
        }

        return $messages;
    }

    private function is_sqlite_integration_active(): bool
    {
        return class_exists('SQLite_Database_Integration') || defined('SQLITE_MAIN_FILE') || file_exists(WP_CONTENT_DIR . '/db.php');
    }

    private function is_flipbook_active(): bool
    {
        global $shortcode_tags;

        return isset($shortcode_tags['fptf-flipbook']) || gwob_is_plugin_active('free-pdf-to-flipbook/free-pdf-to-flipbook.php');
    }
}

function gwob_is_plugin_active(string $plugin): bool
{
    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    return is_plugin_active($plugin);
}
