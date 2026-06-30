<?php
/**
 * mybooks 共通フッター（独立テンプレートで使用）。自己完結。
 * ニュースレターは admin-post 経由で GWOB_Forms が処理する。
 */
if (!defined('ABSPATH')) {
    exit;
}

$mb_books_url = home_url('/books/');
$mb_guide_url = home_url('/upload-book-guide/');

$mb_status = isset($_GET['gwob']) ? sanitize_key(wp_unslash($_GET['gwob'])) : '';
$mb_scope  = isset($_GET['gwob_scope']) ? sanitize_key(wp_unslash($_GET['gwob_scope'])) : '';
$mb_news_done  = ($mb_scope === 'newsletter' && $mb_status === 'subscribed');
$mb_news_error = ($mb_scope === 'newsletter' && $mb_status === 'error');
?>
<footer class="mb-footer" id="contact">
    <div class="mb-container mb-footer-inner">
        <div class="mb-footer-brand">
            <span class="mb-logo mb-logo-light"><?php bloginfo('name'); ?></span>
            <p class="mb-footer-tagline">PDFを、美しいオンライン本へ。</p>
        </div>

        <nav class="mb-footer-col" aria-label="サービス">
            <h4 class="mb-footer-heading">サービス</h4>
            <a href="<?php echo esc_url($mb_guide_url); ?>">PDF本の作り方</a>
            <a href="<?php echo esc_url($mb_books_url); ?>">オンライン本棚</a>
            <a href="<?php echo esc_url(home_url('/pricing/')); ?>">料金プラン</a>
        </nav>

        <nav class="mb-footer-col" aria-label="サポート">
            <h4 class="mb-footer-heading">サポート</h4>
            <a href="<?php echo esc_url($mb_guide_url); ?>">ヘルプセンター</a>
            <a href="<?php echo esc_url(home_url('/faq/')); ?>">よくある質問</a>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>">お問い合わせ</a>
        </nav>

        <nav class="mb-footer-col" aria-label="会社情報">
            <h4 class="mb-footer-heading">会社情報</h4>
            <a href="<?php echo esc_url(home_url('/company/')); ?>">運営会社</a>
            <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">プライバシーポリシー</a>
            <a href="<?php echo esc_url(home_url('/terms/')); ?>">利用規約</a>
        </nav>

        <div class="mb-footer-news" id="newsletter">
            <h4 class="mb-footer-heading">最新情報を受け取る</h4>
            <p class="mb-footer-news-lead">新機能や活用事例をメールでお届けします。</p>

            <?php if ($mb_news_done) : ?>
                <p class="mb-news-msg mb-news-msg-ok">登録ありがとうございます。最新情報をお届けします。</p>
            <?php elseif ($mb_news_error) : ?>
                <p class="mb-news-msg mb-news-msg-ng">メールアドレスをご確認のうえ、もう一度お試しください。</p>
            <?php endif; ?>

            <form class="mb-news-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <input type="hidden" name="action" value="gwob_newsletter">
                <?php wp_nonce_field('gwob_newsletter', 'gwob_newsletter_nonce'); ?>
                <label class="mb-visually-hidden" for="mb-news-email">メールアドレス</label>
                <input id="mb-news-email" class="mb-news-input" type="email" name="gwob_email" placeholder="メールアドレスを入力" autocomplete="email" required>
                <button class="mb-btn mb-btn-primary mb-news-btn" type="submit">登録する</button>
            </form>
        </div>
    </div>

    <div class="mb-container mb-footer-bottom">
        <span>&copy; 2026 mybooks. All rights reserved.</span>
    </div>
</footer>
