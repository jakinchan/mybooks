<?php
/**
 * お問い合わせページ。送信は admin-post 経由で GWOB_Forms が処理し DB に保存する。
 */
if (!defined('ABSPATH')) {
    exit;
}

$status = isset($_GET['gwob']) ? sanitize_key(wp_unslash($_GET['gwob'])) : '';
$scope  = isset($_GET['gwob_scope']) ? sanitize_key(wp_unslash($_GET['gwob_scope'])) : '';
$is_contact_status = ($scope === 'contact' || $scope === '');
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('mb-landing mb-contact'); ?>>
<?php wp_body_open(); ?>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-header.php'; ?>

<main class="mb-main">
    <section class="mb-page-hero">
        <div class="mb-container">
            <p class="mb-kicker">CONTACT</p>
            <h1 class="mb-page-hero-title">お問い合わせ</h1>
            <p class="mb-page-hero-lead">サービスに関するご質問・ご相談はこちらから。通常2〜3営業日以内にご返信します。</p>
        </div>
    </section>

    <section class="mb-section">
        <div class="mb-container mb-form-narrow">
            <?php if ($is_contact_status && $status === 'sent') : ?>
                <div class="mb-alert mb-alert-success" role="status">
                    お問い合わせを受け付けました。ご連絡ありがとうございます。
                </div>
            <?php elseif ($is_contact_status && $status === 'error') : ?>
                <div class="mb-alert mb-alert-error" role="alert">
                    入力内容に不備があります。お名前・メールアドレス・お問い合わせ内容をご確認ください。
                </div>
            <?php endif; ?>

            <form class="mb-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <input type="hidden" name="action" value="gwob_contact">
                <?php wp_nonce_field('gwob_contact', 'gwob_contact_nonce'); ?>

                <div class="mb-field">
                    <label class="mb-label" for="gwob_name">お名前 <span class="mb-required">必須</span></label>
                    <input class="mb-input" type="text" id="gwob_name" name="gwob_name" required>
                </div>

                <div class="mb-field">
                    <label class="mb-label" for="gwob_email">メールアドレス <span class="mb-required">必須</span></label>
                    <input class="mb-input" type="email" id="gwob_email" name="gwob_email" required autocomplete="email">
                </div>

                <div class="mb-field">
                    <label class="mb-label" for="gwob_message">お問い合わせ内容 <span class="mb-required">必須</span></label>
                    <textarea class="mb-input mb-textarea" id="gwob_message" name="gwob_message" rows="6" required></textarea>
                </div>

                <p class="mb-form-note">
                    送信により <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">プライバシーポリシー</a> に同意したものとみなします。
                </p>

                <button class="mb-btn mb-btn-primary mb-form-submit" type="submit">送信する</button>
            </form>
        </div>
    </section>
</main>

<?php include GWOB_PLUGIN_DIR . 'templates/partials/site-footer.php'; ?>

<?php wp_footer(); ?>
</body>
</html>
