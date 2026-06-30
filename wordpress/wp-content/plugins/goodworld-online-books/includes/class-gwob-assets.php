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

        if ($this->is_marketing_page()) {
            wp_enqueue_style('gwob-landing', GWOB_PLUGIN_URL . 'assets/css/landing.css', [], GWOB_VERSION);
            wp_enqueue_script('gwob-landing', GWOB_PLUGIN_URL . 'assets/js/landing.js', [], GWOB_VERSION, true);
        }

        if ($this->should_enqueue_flipbook_assets()) {
            $this->enqueue_flipbook_assets();
        }
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

    private function is_marketing_page(): bool
    {
        return is_front_page()
            || is_page(['books', 'upload-book-guide', 'pricing', 'faq', 'contact', 'company', 'privacy-policy', 'terms'])
            || is_singular(GWOB_Post_Type::POST_TYPE)
            || is_post_type_archive(GWOB_Post_Type::POST_TYPE)
            || is_tax(GWOB_Post_Type::TAXONOMY);
    }

    private function should_enqueue_flipbook_assets(): bool
    {
        if (is_singular(GWOB_Post_Type::POST_TYPE)) {
            return true;
        }

        return is_page('books') || is_post_type_archive(GWOB_Post_Type::POST_TYPE) || is_tax(GWOB_Post_Type::TAXONOMY);
    }

    private function enqueue_flipbook_assets(): void
    {
        if (!defined('WP_PLUGIN_URL') || !is_dir(WP_PLUGIN_DIR . '/free-pdf-to-flipbook')) {
            return;
        }

        wp_enqueue_style('flipstyle-css', plugins_url('free-pdf-to-flipbook/css/flipstyle.css'), [], '3.1.0');
        wp_enqueue_script('pdf-js', plugins_url('free-pdf-to-flipbook/js/pdf.js'), [], '3.5.141', true);
        wp_enqueue_script('jquery');

        // pdf.js が日本語など CID font の CMap / 標準 font を取得できるよう設定を注入するシム。
        // pdf.js の後・flipbook-js の前に実行する必要があるため pdf-js を依存に指定する。
        wp_enqueue_script('gwob-pdfjs-cmap', GWOB_PLUGIN_URL . 'assets/js/pdfjs-cmap-shim.js', ['pdf-js'], GWOB_VERSION, true);
        wp_localize_script('gwob-pdfjs-cmap', 'GWOB_PDFJS', [
            'cMapUrl' => GWOB_PLUGIN_URL . 'assets/pdfjs/cmaps/',
            'standardFontDataUrl' => GWOB_PLUGIN_URL . 'assets/pdfjs/standard_fonts/',
            'workerSrc' => plugins_url('free-pdf-to-flipbook/js/pdf.worker.js'),
        ]);

        wp_enqueue_script('turn-js', plugins_url('free-pdf-to-flipbook/js/turnV5.js'), ['jquery'], '3.1.0', true);
        wp_enqueue_script('flipbook-js', plugins_url('free-pdf-to-flipbook/js/fptf-flipbook.js'), ['jquery', 'turn-js', 'gwob-pdfjs-cmap'], '3.1.0', true);
    }
}
