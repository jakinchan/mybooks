<?php
/**
 * よくある質問ページ。details/summary によるノーJSアコーディオン。
 */
if (!defined('ABSPATH')) {
    exit;
}

$contact_url = home_url('/contact/');

$faqs = [
    [
        'q' => 'どんなPDFでもオンライン本にできますか？',
        'a' => 'はい。教材・社内マニュアル・カタログ・作品集など、一般的なPDFをアップロードしてめくれるオンライン本にできます。日本語フォントを含むPDFにも対応しています。',
    ],
    [
        'q' => 'スマートフォンでも閲覧できますか？',
        'a' => 'PC・タブレット・スマートフォンに対応しています。画面サイズに合わせて自動的にレイアウトが調整されます。',
    ],
    [
        'q' => '公開範囲は設定できますか？',
        'a' => '本ごとに「全体公開」「ログイン限定」「限定公開（管理者・編集者のみ）」の3段階から選べます。社内資料や会員向け教材にもご利用いただけます。',
    ],
    [
        'q' => '作った本はどうやって共有しますか？',
        'a' => '公開すると専用のURLが発行されます。URLをメールやSNSで送るだけで、相手はそのままブラウザで閲覧できます。',
    ],
    [
        'q' => '料金はかかりますか？',
        'a' => '無料プランから始められます。より多くの本を公開したり、表示のカスタマイズや権限管理が必要な場合は有料プランをご利用ください。',
    ],
    [
        'q' => 'PDFの差し替えや削除はできますか？',
        'a' => '管理画面からいつでも本の内容を編集・非公開・削除できます。差し替え後は公開ページに自動で反映されます。',
    ],
];
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('mb-landing mb-faq'); ?>>
<?php wp_body_open(); ?>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-header.php'; ?>

<main class="mb-main">
    <section class="mb-page-hero">
        <div class="mb-container">
            <p class="mb-kicker">FAQ</p>
            <h1 class="mb-page-hero-title">よくある質問</h1>
            <p class="mb-page-hero-lead">サービスについてよくいただくご質問をまとめました。</p>
        </div>
    </section>

    <section class="mb-section">
        <div class="mb-container mb-doc-narrow">
            <div class="mb-faq-list">
                <?php foreach ($faqs as $index => $faq) : ?>
                    <details class="mb-faq-item"<?php echo $index === 0 ? ' open' : ''; ?>>
                        <summary class="mb-faq-q">
                            <span><?php echo esc_html($faq['q']); ?></span>
                            <span class="mb-faq-mark" aria-hidden="true"></span>
                        </summary>
                        <div class="mb-faq-a"><?php echo esc_html($faq['a']); ?></div>
                    </details>
                <?php endforeach; ?>
            </div>

            <div class="mb-faq-cta">
                <p>解決しない場合は、お気軽にお問い合わせください。</p>
                <a class="mb-btn mb-btn-primary" href="<?php echo esc_url($contact_url); ?>">お問い合わせ</a>
            </div>
        </div>
    </section>
</main>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-footer.php'; ?>

<?php wp_footer(); ?>
</body>
</html>
