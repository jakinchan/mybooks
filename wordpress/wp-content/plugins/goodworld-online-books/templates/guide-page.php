<?php
/**
 * 「PDF本の作り方」ガイドページ（独立テンプレート）。
 * ランディングと同じヘッダー／フッターで統一し、手順をタイムライン表示する。
 */
if (!defined('ABSPATH')) {
    exit;
}

$books_url = home_url('/books/');
$admin_flipbook_url = admin_url('admin.php?page=fptf-flipbook-converter');
$admin_new_book_url = admin_url('post-new.php?post_type=' . GWOB_Post_Type::POST_TYPE);

$steps = [
    [
        'title' => 'PDFを用意する',
        'desc'  => '教材・社内マニュアル・カタログ・作品集など、本にしたいPDFを準備します。',
    ],
    [
        'title' => 'Free PDF to Flipbook を開く',
        'desc'  => 'WordPress 管理画面の「PDF TO FLIPBOOK」メニューを開きます。',
    ],
    [
        'title' => 'PDFをアップロード',
        'desc'  => 'ファイルを選択するか、メディアライブラリから既存のPDFを選びます。',
    ],
    [
        'title' => 'ショートコードをコピー',
        'desc'  => '生成された <code>[fptf-flipbook pdf="..."]</code> ショートコードをコピーします。',
        'raw'   => true,
    ],
    [
        'title' => 'オンライン本を新規追加',
        'desc'  => '「オンライン本」投稿を新規作成し、タイトルや表紙、説明文を設定します。',
    ],
    [
        'title' => 'ショートコードを貼り付け',
        'desc'  => '「Free PDF to Flipbook ショートコード」欄にコピーしたコードを貼り付けます。',
    ],
    [
        'title' => '公開範囲を設定して公開',
        'desc'  => '公開範囲（全体公開／ログイン限定／限定公開）を選び、公開ボタンを押します。',
    ],
    [
        'title' => 'オンライン本棚に表示',
        'desc'  => '公開した本はオンライン本棚ページに自動で並び、めくって読めるようになります。',
    ],
];

$access_levels = [
    [
        'badge' => 'public',
        'title' => '全体公開',
        'desc'  => '誰でも閲覧できます。一般公開のカタログや作品集に最適です。',
    ],
    [
        'badge' => 'logged_in',
        'title' => 'ログイン限定',
        'desc'  => 'ログインユーザーのみ閲覧できます。会員向け教材などに。',
    ],
    [
        'badge' => 'private',
        'title' => '限定公開',
        'desc'  => '管理者・編集者のみ閲覧できます。社内マニュアルの下書き確認などに。',
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
<body <?php body_class('mb-landing mb-guide'); ?>>
<?php wp_body_open(); ?>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-header.php'; ?>

<main class="mb-main">

    <!-- Guide hero -->
    <section class="mb-guide-hero">
        <div class="mb-container mb-guide-hero-inner">
            <p class="mb-kicker">GUIDE</p>
            <h1 class="mb-guide-title">PDF本の作り方</h1>
            <p class="mb-guide-lead">
                PDFを用意してアップロードするだけ。たった8ステップで、めくれるオンライン本を公開できます。<br>
                はじめての方でも、画面の案内に沿って迷わず作成できます。
            </p>
            <div class="mb-guide-actions">
                <a class="mb-btn mb-btn-primary" href="<?php echo esc_url($admin_flipbook_url); ?>">管理画面を開く<span class="mb-btn-arrow" aria-hidden="true">→</span></a>
                <a class="mb-btn mb-btn-ghost" href="<?php echo esc_url($books_url); ?>">オンライン本棚を見る</a>
            </div>
        </div>
    </section>

    <!-- Steps timeline -->
    <section class="mb-section mb-guide-steps-section">
        <div class="mb-container mb-guide-narrow">
            <ol class="mb-timeline">
                <?php foreach ($steps as $index => $step) : ?>
                    <li class="mb-timeline-item">
                        <span class="mb-timeline-badge"><?php echo esc_html((string) ($index + 1)); ?></span>
                        <div class="mb-timeline-card">
                            <h3 class="mb-timeline-title"><?php echo esc_html($step['title']); ?></h3>
                            <p class="mb-timeline-desc">
                                <?php
                                if (!empty($step['raw'])) {
                                    echo wp_kses($step['desc'], ['code' => []]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                } else {
                                    echo esc_html($step['desc']);
                                }
                                ?>
                            </p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <!-- 公開範囲について -->
    <section class="mb-section mb-access-section">
        <div class="mb-container">
            <h2 class="mb-section-title">公開範囲について</h2>
            <p class="mb-access-lead">本ごとに3段階の公開範囲を選べます。用途に合わせて使い分けてください。</p>
            <div class="mb-access-grid">
                <?php foreach ($access_levels as $level) : ?>
                    <article class="mb-access-card">
                        <span class="mb-access-badge"><?php echo esc_html($level['badge']); ?></span>
                        <h3 class="mb-access-title"><?php echo esc_html($level['title']); ?></h3>
                        <p class="mb-access-desc"><?php echo esc_html($level['desc']); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA band -->
    <section class="mb-guide-cta-section">
        <div class="mb-container">
            <div class="mb-guide-cta">
                <div class="mb-guide-cta-text">
                    <h2>さっそく最初の一冊を作りましょう</h2>
                    <p>PDFがあれば数分で公開できます。まずは管理画面から試してみてください。</p>
                </div>
                <div class="mb-guide-cta-actions">
                    <a class="mb-btn mb-btn-light" href="<?php echo esc_url($admin_new_book_url); ?>">オンライン本を追加</a>
                    <a class="mb-btn mb-btn-outline-light" href="<?php echo esc_url($books_url); ?>">本棚を見る</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-footer.php'; ?>

<?php wp_footer(); ?>
</body>
</html>
