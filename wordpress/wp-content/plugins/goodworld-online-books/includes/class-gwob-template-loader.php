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
        // フロントページ（投稿一覧でも固定ページでも）をサービス紹介トップに差し替える。
        if (is_front_page()) {
            return GWOB_PLUGIN_DIR . 'templates/landing-page.php';
        }

        if (is_singular(GWOB_Post_Type::POST_TYPE)) {
            return GWOB_PLUGIN_DIR . 'templates/single-book.php';
        }

        if (is_page('upload-book-guide')) {
            return GWOB_PLUGIN_DIR . 'templates/guide-page.php';
        }

        if (is_page('pricing')) {
            return GWOB_PLUGIN_DIR . 'templates/pricing-page.php';
        }

        if (is_page('faq')) {
            return GWOB_PLUGIN_DIR . 'templates/faq-page.php';
        }

        if (is_page('contact')) {
            return GWOB_PLUGIN_DIR . 'templates/contact-page.php';
        }

        if (is_page(['company', 'privacy-policy', 'terms'])) {
            return GWOB_PLUGIN_DIR . 'templates/page-simple.php';
        }

        if (is_page('books')) {
            return GWOB_PLUGIN_DIR . 'templates/archive-book.php';
        }

        if (is_post_type_archive(GWOB_Post_Type::POST_TYPE) || is_tax(GWOB_Post_Type::TAXONOMY)) {
            return GWOB_PLUGIN_DIR . 'templates/archive-book.php';
        }

        return $template;
    }
}
