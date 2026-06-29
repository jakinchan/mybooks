<?php
/**
 * Plugin Name: GoodWorld Online Books
 * Description: PDF bookshelves for WordPress + SQLite + Free PDF to Flipbook.
 * Version: 0.1.0
 * Author: GoodWorld
 * Text Domain: goodworld-online-books
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('GWOB_VERSION', '0.1.0');
define('GWOB_PLUGIN_FILE', __FILE__);
define('GWOB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GWOB_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once GWOB_PLUGIN_DIR . 'includes/class-gwob-plugin.php';
require_once GWOB_PLUGIN_DIR . 'includes/class-gwob-dependency-checker.php';
require_once GWOB_PLUGIN_DIR . 'includes/class-gwob-post-type.php';
require_once GWOB_PLUGIN_DIR . 'includes/class-gwob-admin.php';
require_once GWOB_PLUGIN_DIR . 'includes/class-gwob-assets.php';
require_once GWOB_PLUGIN_DIR . 'includes/class-gwob-access-control.php';
require_once GWOB_PLUGIN_DIR . 'includes/class-gwob-template-loader.php';
require_once GWOB_PLUGIN_DIR . 'includes/class-gwob-shortcodes.php';

function gwob(): GWOB_Plugin
{
    static $plugin = null;

    if ($plugin === null) {
        $plugin = new GWOB_Plugin();
    }

    return $plugin;
}

register_activation_hook(__FILE__, ['GWOB_Plugin', 'activate']);
register_deactivation_hook(__FILE__, ['GWOB_Plugin', 'deactivate']);

gwob()->run();
