<?php
/**
 * mybooks サービス紹介トップページ（独立テンプレート）。
 * GoodWorld Online Books が template_include でフロントページに差し込む。
 * テーマに依存せず、ヘッダー〜フッターまで自前で描画する。
 */
if (!defined('ABSPATH')) {
    exit;
}

$home_url  = home_url('/');
$books_url = home_url('/books/');
$guide_url = home_url('/upload-book-guide/');

// デモ用にサンプル本があればその個別ページへ、無ければ本棚へ。
$demo_url = $books_url;
$sample   = get_page_by_path('sample-goodworld-online-book', OBJECT, GWOB_Post_Type::POST_TYPE);
if ($sample instanceof WP_Post) {
    $demo_url = get_permalink($sample);
}

// おすすめの本。公開済みの実データを優先し、足りない分はサンプルで補完する。
// 表紙はサムネイルがあれば画像、無ければ CSS のカラー variant で描画する。
$variants = ['a', 'b', 'c', 'd', 'e', 'f'];

$fallback_books = [
    ['title' => '旅する日本の絶景', 'category' => '写真集'],
    ['title' => 'やさしい暮らしのレシピ', 'category' => 'レシピ集'],
    ['title' => '北欧インテリアの教科書', 'category' => 'インテリア'],
    ['title' => '小さな会社のブランド戦略', 'category' => 'ビジネス'],
    ['title' => '観葉植物と暮らす', 'category' => 'ガイドブック'],
    ['title' => 'パリ散歩ガイド', 'category' => '旅行ガイド'],
];

$sample_books = [];

// おすすめフラグ付きを優先しつつ、無ければ最新の公開本で埋める。
// メタの有無に依存しないよう、おすすめ→最新の順で2段階に取得する。
$collected_ids = [];

$featured_ids = get_posts([
    'post_type'      => GWOB_Post_Type::POST_TYPE,
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'fields'         => 'ids',
    'meta_query'     => [['key' => '_gwob_featured', 'value' => '1']],
    'orderby'        => ['menu_order' => 'ASC', 'date' => 'DESC'],
]);

$recent_ids = get_posts([
    'post_type'      => GWOB_Post_Type::POST_TYPE,
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'fields'         => 'ids',
    'orderby'        => ['menu_order' => 'ASC', 'date' => 'DESC'],
]);

$ordered_ids = array_values(array_unique(array_merge($featured_ids, $recent_ids)));

$featured_query = new WP_Query([
    'post_type'      => GWOB_Post_Type::POST_TYPE,
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'post__in'       => !empty($ordered_ids) ? $ordered_ids : [0],
    'orderby'        => 'post__in',
]);

if ($featured_query->have_posts()) {
    while ($featured_query->have_posts()) {
        $featured_query->the_post();
        $book_id = get_the_ID();

        if (!GWOB_Access_Control::can_view($book_id)) {
            continue;
        }

        $terms = get_the_terms($book_id, GWOB_Post_Type::TAXONOMY);
        $category = (is_array($terms) && !empty($terms)) ? $terms[0]->name : 'オンライン本';

        $sample_books[] = [
            'title'    => get_the_title(),
            'category' => $category,
            'url'      => get_permalink($book_id),
            'thumb'    => has_post_thumbnail($book_id) ? get_the_post_thumbnail_url($book_id, 'medium') : '',
        ];
    }
}
wp_reset_postdata();

// 6 枠に満たない分はサンプルで補完。
foreach ($fallback_books as $fallback) {
    if (count($sample_books) >= 6) {
        break;
    }
    $sample_books[] = $fallback;
}

// variant を割り当て。
foreach ($sample_books as $i => &$book_ref) {
    $book_ref['variant'] = $variants[$i % count($variants)];
}
unset($book_ref);

$features = [
    [
        'title' => 'PDFアップロード',
        'desc'  => 'ドラッグ＆ドロップで簡単にPDFを登録できます。',
        'icon'  => 'upload',
    ],
    [
        'title' => '本棚で管理',
        'desc'  => '公開中の本、下書き、カテゴリをまとめて管理できます。',
        'icon'  => 'shelf',
    ],
    [
        'title' => 'スマホ対応',
        'desc'  => 'PC、タブレット、スマートフォンで快適に閲覧できます。',
        'icon'  => 'mobile',
    ],
    [
        'title' => '共有リンク発行',
        'desc'  => 'URLを送るだけで、顧客や社内メンバーに共有できます。',
        'icon'  => 'link',
    ],
];

$steps = [
    ['title' => 'PDFをアップロード', 'desc' => 'PDFファイルを選ぶだけで、すぐに取り込みが完了します。'],
    ['title' => '本のデザインをカスタマイズ', 'desc' => '表紙、タイトル、説明文を設定して、自分だけの一冊に仕上げます。'],
    ['title' => '公開して共有', 'desc' => '公開リンクを発行して、SNSやメールで簡単に共有できます。'],
];

/**
 * 機能カード用のインライン SVG アイコン。外部依存なし。
 */
if (!function_exists('gwob_landing_icon')) :
function gwob_landing_icon(string $name): string
{
    $paths = [
        'upload' => '<path d="M12 16V6m0 0L8 10m4-4 4 4" /><path d="M5 16v2a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-2" />',
        'shelf'  => '<path d="M4 5v14M8 5v14M14 6l4 13" /><path d="M3 19h18" />',
        'mobile' => '<rect x="7" y="3" width="10" height="18" rx="2.5" /><path d="M11 17.5h2" />',
        'link'   => '<path d="M9 13a4 4 0 0 0 5.66 0l2.34-2.34a4 4 0 1 0-5.66-5.66L10 6.34" /><path d="M15 11a4 4 0 0 0-5.66 0L7 13.34a4 4 0 1 0 5.66 5.66L14 17.66" />',
    ];
    $inner = $paths[$name] ?? '';

    return '<svg class="mb-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $inner . '</svg>';
}
endif;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('mb-landing'); ?>>
<?php wp_body_open(); ?>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-header.php'; ?>

<main class="mb-main">

    <!-- Hero -->
    <section class="mb-hero">
        <div class="mb-container mb-hero-inner">
            <div class="mb-hero-copy">
                <h1 class="mb-hero-title">PDFを、美しい<br>オンライン本へ。</h1>
                <p class="mb-hero-lead">
                    PDFをアップロードするだけで、めくれる本のようなオンラインブックを作成できます。<br>
                    教材、社内マニュアル、カタログ、作品集を、誰でも簡単に公開できます。
                </p>
                <div class="mb-hero-actions">
                    <a class="mb-btn mb-btn-primary" href="<?php echo esc_url($books_url); ?>">今すぐ試す<span class="mb-btn-arrow" aria-hidden="true">→</span></a>
                    <a class="mb-btn mb-btn-ghost" href="<?php echo esc_url($demo_url); ?>">
                        <span class="mb-btn-play" aria-hidden="true">▶</span>デモを見る
                    </a>
                </div>
            </div>

            <!-- ノートPC + 開いた本の CSS モック -->
            <div class="mb-hero-visual" aria-hidden="true">
                <div class="mb-laptop">
                    <div class="mb-laptop-screen">
                        <div class="mb-book">
                            <div class="mb-book-page mb-book-page-left">
                                <span class="mb-page-head">KINFOLK</span>
                                <span class="mb-page-line"></span>
                                <span class="mb-page-line"></span>
                                <span class="mb-page-line short"></span>
                                <span class="mb-page-line"></span>
                                <span class="mb-page-line short"></span>
                            </div>
                            <div class="mb-book-page mb-book-page-right">
                                <span class="mb-page-photo"></span>
                                <span class="mb-page-line"></span>
                                <span class="mb-page-line short"></span>
                            </div>
                            <span class="mb-book-gutter"></span>
                        </div>
                        <div class="mb-book-toolbar">
                            <span class="mb-tool-dot"></span>
                            <span class="mb-tool-dot"></span>
                            <span class="mb-tool-pages">4-5 / 32</span>
                            <span class="mb-tool-dot"></span>
                            <span class="mb-tool-dot"></span>
                        </div>
                    </div>
                    <div class="mb-laptop-base"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature cards -->
    <section class="mb-section mb-features-section">
        <div class="mb-container">
            <div class="mb-features">
                <?php foreach ($features as $feature) : ?>
                    <article class="mb-feature-card">
                        <span class="mb-feature-icon"><?php echo gwob_landing_icon($feature['icon']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                        <h3 class="mb-feature-title"><?php echo esc_html($feature['title']); ?></h3>
                        <p class="mb-feature-desc"><?php echo esc_html($feature['desc']); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- おすすめの本 -->
    <section class="mb-section mb-books-section">
        <div class="mb-container">
            <h2 class="mb-section-title">おすすめの本</h2>
            <div class="mb-books">
                <?php foreach ($sample_books as $book) :
                    $book_url = !empty($book['url']) ? $book['url'] : $books_url;
                    $has_thumb = !empty($book['thumb']);
                    ?>
                    <article class="mb-book-card">
                        <a class="mb-book-cover mb-cover-<?php echo esc_attr($book['variant']); ?><?php echo $has_thumb ? ' mb-cover-has-image' : ''; ?>" href="<?php echo esc_url($book_url); ?>">
                            <span class="mb-book-spine"></span>
                            <?php if ($has_thumb) : ?>
                                <img class="mb-book-cover-img" src="<?php echo esc_url($book['thumb']); ?>" alt="<?php echo esc_attr($book['title']); ?>" loading="lazy">
                            <?php else : ?>
                                <span class="mb-book-cover-title"><?php echo esc_html($book['title']); ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="mb-book-meta">
                            <h3 class="mb-book-title"><a href="<?php echo esc_url($book_url); ?>"><?php echo esc_html($book['title']); ?></a></h3>
                            <span class="mb-book-category"><?php echo esc_html($book['category']); ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 使い方 3ステップ -->
    <section class="mb-section mb-steps-section">
        <div class="mb-container">
            <h2 class="mb-section-title">使い方はかんたん、3ステップ</h2>
            <div class="mb-steps">
                <?php foreach ($steps as $index => $step) : ?>
                    <article class="mb-step-card">
                        <span class="mb-step-badge"><?php echo esc_html((string) ($index + 1)); ?></span>
                        <h3 class="mb-step-title"><?php echo esc_html($step['title']); ?></h3>
                        <p class="mb-step-desc"><?php echo esc_html($step['desc']); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</main>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-footer.php'; ?>

<?php wp_footer(); ?>
</body>
</html>
