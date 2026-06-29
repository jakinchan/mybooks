<?php

if (!defined('ABSPATH')) {
    exit;
}

class GWOB_Template_Loader
{
    public function register(): void
    {
        add_filter('template_include', [$this, 'template_include']);
    }

    public function template_include(string $template): string
    {
        if (is_singular(GWOB_Post_Type::POST_TYPE)) {
            return GWOB_PLUGIN_DIR . 'templates/single-book.php';
        }

        if (is_post_type_archive(GWOB_Post_Type::POST_TYPE) || is_tax(GWOB_Post_Type::TAXONOMY)) {
            return GWOB_PLUGIN_DIR . 'templates/archive-book.php';
        }

        return $template;
    }
}
