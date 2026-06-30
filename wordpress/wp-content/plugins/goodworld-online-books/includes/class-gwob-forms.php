<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * お問い合わせ・ニュースレター購読のフロント送信を処理し、DB に保存する。
 * 送信内容は管理画面「お問い合わせ」(gwob_inquiry CPT) で確認できる。
 * 外部メール送信は best-effort（wp_mail）。失敗してもフォーム自体は成功扱い。
 */
class GWOB_Forms
{
    public const CPT = 'gwob_inquiry';

    private const CONTACT_ACTION = 'gwob_contact';
    private const NEWSLETTER_ACTION = 'gwob_newsletter';

    public function register(): void
    {
        add_action('init', [$this, 'register_cpt']);

        add_action('admin_post_' . self::CONTACT_ACTION, [$this, 'handle_contact']);
        add_action('admin_post_nopriv_' . self::CONTACT_ACTION, [$this, 'handle_contact']);

        add_action('admin_post_' . self::NEWSLETTER_ACTION, [$this, 'handle_newsletter']);
        add_action('admin_post_nopriv_' . self::NEWSLETTER_ACTION, [$this, 'handle_newsletter']);

        add_filter('manage_' . self::CPT . '_posts_columns', [$this, 'columns']);
        add_action('manage_' . self::CPT . '_posts_custom_column', [$this, 'column_content'], 10, 2);
    }

    public function register_cpt(): void
    {
        register_post_type(self::CPT, [
            'labels' => [
                'name' => 'お問い合わせ',
                'singular_name' => 'お問い合わせ',
                'menu_name' => 'お問い合わせ',
                'all_items' => '受信一覧',
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-email',
            'capability_type' => 'post',
            'capabilities' => [
                'create_posts' => 'do_not_allow',
            ],
            'map_meta_cap' => true,
            'supports' => ['title'],
            'menu_position' => 26,
        ]);
    }

    public function handle_contact(): void
    {
        $redirect = $this->referer_or(home_url('/contact/'));

        if (!isset($_POST['gwob_contact_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['gwob_contact_nonce'])), self::CONTACT_ACTION)) {
            $this->redirect_with($redirect, 'error');
        }

        $name    = isset($_POST['gwob_name']) ? sanitize_text_field(wp_unslash($_POST['gwob_name'])) : '';
        $email   = isset($_POST['gwob_email']) ? sanitize_email(wp_unslash($_POST['gwob_email'])) : '';
        $message = isset($_POST['gwob_message']) ? sanitize_textarea_field(wp_unslash($_POST['gwob_message'])) : '';

        if ($name === '' || !is_email($email) || $message === '') {
            $this->redirect_with($redirect, 'error');
        }

        $inquiry_id = wp_insert_post([
            'post_type'   => self::CPT,
            'post_status' => 'publish',
            'post_title'  => sprintf('お問い合わせ: %s', $name),
        ]);

        if (!is_wp_error($inquiry_id) && $inquiry_id) {
            update_post_meta($inquiry_id, '_gwob_inquiry_type', 'contact');
            update_post_meta($inquiry_id, '_gwob_inquiry_name', $name);
            update_post_meta($inquiry_id, '_gwob_inquiry_email', $email);
            update_post_meta($inquiry_id, '_gwob_inquiry_message', $message);

            $this->notify_admin(
                sprintf('[mybooks] 新しいお問い合わせ: %s', $name),
                sprintf("お名前: %s\nメール: %s\n\n%s", $name, $email, $message),
                $email
            );
        }

        $this->redirect_with($redirect, 'sent');
    }

    public function handle_newsletter(): void
    {
        $redirect = $this->referer_or(home_url('/'));

        if (!isset($_POST['gwob_newsletter_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['gwob_newsletter_nonce'])), self::NEWSLETTER_ACTION)) {
            $this->redirect_with($redirect, 'error', 'newsletter');
        }

        $email = isset($_POST['gwob_email']) ? sanitize_email(wp_unslash($_POST['gwob_email'])) : '';

        if (!is_email($email)) {
            $this->redirect_with($redirect, 'error', 'newsletter');
        }

        if (!$this->subscriber_exists($email)) {
            $subscriber_id = wp_insert_post([
                'post_type'   => self::CPT,
                'post_status' => 'publish',
                'post_title'  => sprintf('購読: %s', $email),
            ]);

            if (!is_wp_error($subscriber_id) && $subscriber_id) {
                update_post_meta($subscriber_id, '_gwob_inquiry_type', 'newsletter');
                update_post_meta($subscriber_id, '_gwob_inquiry_email', $email);
            }
        }

        $this->redirect_with($redirect, 'subscribed', 'newsletter');
    }

    public function columns(array $columns): array
    {
        $reordered = [
            'cb' => $columns['cb'] ?? '<input type="checkbox" />',
            'title' => 'タイトル',
            'gwob_type' => '種別',
            'gwob_email' => 'メール',
            'date' => $columns['date'] ?? '日付',
        ];

        return $reordered;
    }

    public function column_content(string $column, int $post_id): void
    {
        if ($column === 'gwob_type') {
            $type = get_post_meta($post_id, '_gwob_inquiry_type', true);
            echo esc_html($type === 'newsletter' ? 'ニュースレター' : 'お問い合わせ');
        } elseif ($column === 'gwob_email') {
            echo esc_html(get_post_meta($post_id, '_gwob_inquiry_email', true));
        }
    }

    private function subscriber_exists(string $email): bool
    {
        $existing = get_posts([
            'post_type'   => self::CPT,
            'post_status' => 'publish',
            'meta_query'  => [
                ['key' => '_gwob_inquiry_type', 'value' => 'newsletter'],
                ['key' => '_gwob_inquiry_email', 'value' => $email],
            ],
            'fields'         => 'ids',
            'posts_per_page' => 1,
        ]);

        return !empty($existing);
    }

    private function notify_admin(string $subject, string $body, string $reply_to): void
    {
        $admin_email = get_option('admin_email');
        if (!$admin_email) {
            return;
        }

        $headers = [];
        if (is_email($reply_to)) {
            $headers[] = 'Reply-To: ' . $reply_to;
        }

        // SMTP 未設定環境では送信に失敗するが、保存は済んでいるため握りつぶす。
        @wp_mail($admin_email, $subject, $body, $headers); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
    }

    private function referer_or(string $fallback): string
    {
        $referer = wp_get_referer();

        return $referer ? $referer : $fallback;
    }

    private function redirect_with(string $url, string $status, string $scope = 'contact'): void
    {
        $url = remove_query_arg(['gwob', 'gwob_scope'], $url);
        $url = add_query_arg(['gwob' => $status, 'gwob_scope' => $scope], $url);

        if ($scope === 'newsletter') {
            $url .= '#newsletter';
        }

        wp_safe_redirect($url);
        exit;
    }
}
