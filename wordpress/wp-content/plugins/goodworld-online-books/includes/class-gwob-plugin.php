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
        (new GWOB_Forms())->register();
    }

    public function load_textdomain(): void
    {
        load_plugin_textdomain('goodworld-online-books', false, dirname(plugin_basename(GWOB_PLUGIN_FILE)) . '/languages');
    }

    public static function activate(): void
    {
        require_once GWOB_PLUGIN_DIR . 'includes/class-gwob-post-type.php';
        require_once GWOB_PLUGIN_DIR . 'includes/class-gwob-forms.php';
        (new GWOB_Post_Type())->register_types();
        (new GWOB_Forms())->register_cpt();
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
            ['title' => '料金プラン', 'slug' => 'pricing', 'content' => ''],
            ['title' => 'よくある質問', 'slug' => 'faq', 'content' => ''],
            ['title' => 'お問い合わせ', 'slug' => 'contact', 'content' => ''],
            ['title' => '運営会社', 'slug' => 'company', 'content' => self::company_content()],
            ['title' => 'プライバシーポリシー', 'slug' => 'privacy-policy', 'content' => self::privacy_content()],
            ['title' => '利用規約', 'slug' => 'terms', 'content' => self::terms_content()],
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

    private static function company_content(): string
    {
        return implode("\n\n", [
            '<h2>運営会社</h2>',
            '<p>mybooks は、PDF をめくれるオンライン本として公開できるサービスです。教材・社内マニュアル・カタログ・作品集などを、誰でも簡単にデジタルブック化できます。</p>',
            '<h3>会社概要</h3>',
            '<ul><li>サービス名: mybooks</li><li>事業内容: オンラインブック作成・公開サービスの提供</li><li>お問い合わせ: 本サイトのお問い合わせフォームよりご連絡ください。</li></ul>',
            '<p>※ 本ページの会社情報はサンプルです。公開前に正式な内容へ差し替えてください。</p>',
        ]);
    }

    private static function privacy_content(): string
    {
        return implode("\n\n", [
            '<p>mybooks（以下「当サービス」）は、利用者の個人情報を適切に取り扱います。本ポリシーは、当サービスにおける個人情報の取り扱いについて定めるものです。</p>',
            '<h3>1. 取得する情報</h3>',
            '<p>お問い合わせフォームやニュースレター登録時に、お名前・メールアドレス・お問い合わせ内容を取得する場合があります。</p>',
            '<h3>2. 利用目的</h3>',
            '<p>取得した情報は、お問い合わせへの対応、サービスに関するご案内、サービス改善のために利用します。</p>',
            '<h3>3. 第三者提供</h3>',
            '<p>法令に基づく場合を除き、ご本人の同意なく第三者へ個人情報を提供することはありません。</p>',
            '<h3>4. お問い合わせ窓口</h3>',
            '<p>本ポリシーに関するお問い合わせは、お問い合わせフォームよりご連絡ください。</p>',
            '<p>※ 本ポリシーはサンプルです。公開前に専門家の確認の上、正式な内容へ差し替えてください。</p>',
        ]);
    }

    private static function terms_content(): string
    {
        return implode("\n\n", [
            '<p>本利用規約（以下「本規約」）は、mybooks（以下「当サービス」）の利用条件を定めるものです。利用者は本規約に同意の上、当サービスを利用するものとします。</p>',
            '<h3>1. 適用</h3>',
            '<p>本規約は、利用者と当サービスとの間の一切の関係に適用されます。</p>',
            '<h3>2. 禁止事項</h3>',
            '<p>利用者は、法令違反、第三者の権利侵害、当サービスの運営を妨げる行為を行ってはなりません。アップロードするコンテンツについて、必要な権利を有していることを保証するものとします。</p>',
            '<h3>3. 免責事項</h3>',
            '<p>当サービスは、提供するコンテンツの正確性・完全性について保証しません。利用者が当サービスを利用して生じた損害について、当サービスは責任を負いません。</p>',
            '<h3>4. 規約の変更</h3>',
            '<p>当サービスは、必要と判断した場合、本規約を変更できるものとします。</p>',
            '<p>※ 本規約はサンプルです。公開前に専門家の確認の上、正式な内容へ差し替えてください。</p>',
        ]);
    }
}
