<?php

if (!defined('ABSPATH')) {
    exit;
}

class GWOB_Access_Control
{
    public static function can_view(int $post_id): bool
    {
        $level = get_post_meta($post_id, '_gwob_access_level', true) ?: 'public';

        if ($level === 'public') {
            return true;
        }

        if ($level === 'logged_in') {
            return is_user_logged_in();
        }

        if ($level === 'private') {
            return current_user_can('edit_post', $post_id);
        }

        return false;
    }

    public static function denied_message(): string
    {
        return '<div class="gwob-access-denied">この本を閲覧する権限がありません。</div>';
    }
}
