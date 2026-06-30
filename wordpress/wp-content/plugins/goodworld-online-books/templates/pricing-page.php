<?php
/**
 * 料金プランページ。3 プランをカードで表示する。
 */
if (!defined('ABSPATH')) {
    exit;
}

$contact_url = home_url('/contact/');
$guide_url   = home_url('/upload-book-guide/');

$plans = [
    [
        'name'     => 'Free',
        'price'    => '¥0',
        'unit'     => '/月',
        'tagline'  => 'まずは試してみたい方に',
        'features' => ['オンライン本 3冊まで', 'PDFアップロード', '共有リンク発行', 'mybooks クレジット表示'],
        'cta'      => 'いますぐ始める',
        'cta_url'  => $guide_url,
        'featured' => false,
    ],
    [
        'name'     => 'Pro',
        'price'    => '¥1,480',
        'unit'     => '/月',
        'tagline'  => '本格的に活用するチームに',
        'features' => ['オンライン本 無制限', '公開範囲の細かな設定', '独自の表紙・カスタマイズ', 'クレジット非表示', '優先サポート'],
        'cta'      => 'Pro を始める',
        'cta_url'  => $contact_url,
        'featured' => true,
    ],
    [
        'name'     => 'Business',
        'price'    => 'お問い合わせ',
        'unit'     => '',
        'tagline'  => '組織・大規模運用に',
        'features' => ['Pro の全機能', 'メンバー権限管理', 'アクセス解析', '独自ドメイン対応', '導入サポート'],
        'cta'      => '相談する',
        'cta_url'  => $contact_url,
        'featured' => false,
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
<body <?php body_class('mb-landing mb-pricing'); ?>>
<?php wp_body_open(); ?>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-header.php'; ?>

<main class="mb-main">
    <section class="mb-page-hero">
        <div class="mb-container">
            <p class="mb-kicker">PRICING</p>
            <h1 class="mb-page-hero-title">料金プラン</h1>
            <p class="mb-page-hero-lead">無料で始めて、必要になったらアップグレード。いつでもプラン変更できます。</p>
        </div>
    </section>

    <section class="mb-section">
        <div class="mb-container">
            <div class="mb-plans">
                <?php foreach ($plans as $plan) : ?>
                    <article class="mb-plan-card<?php echo $plan['featured'] ? ' mb-plan-featured' : ''; ?>">
                        <?php if ($plan['featured']) : ?>
                            <span class="mb-plan-tag">おすすめ</span>
                        <?php endif; ?>
                        <h2 class="mb-plan-name"><?php echo esc_html($plan['name']); ?></h2>
                        <p class="mb-plan-tagline"><?php echo esc_html($plan['tagline']); ?></p>
                        <p class="mb-plan-price">
                            <span class="mb-plan-amount"><?php echo esc_html($plan['price']); ?></span>
                            <?php if ($plan['unit']) : ?><span class="mb-plan-unit"><?php echo esc_html($plan['unit']); ?></span><?php endif; ?>
                        </p>
                        <ul class="mb-plan-features">
                            <?php foreach ($plan['features'] as $feature) : ?>
                                <li><span class="mb-plan-check" aria-hidden="true">✓</span><?php echo esc_html($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a class="mb-btn <?php echo $plan['featured'] ? 'mb-btn-primary' : 'mb-btn-ghost'; ?> mb-plan-cta" href="<?php echo esc_url($plan['cta_url']); ?>"><?php echo esc_html($plan['cta']); ?></a>
                    </article>
                <?php endforeach; ?>
            </div>
            <p class="mb-pricing-note">表示価格は税込のイメージです。実際の提供内容・価格は公開前に確定してください。</p>
        </div>
    </section>
</main>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-footer.php'; ?>

<?php wp_footer(); ?>
</body>
</html>
