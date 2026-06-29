<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="gwob-admin-fields">
    <p class="gwob-admin-note">Free PDF to Flipbook で生成したショートコードを貼り付けてください。許可されていないショートコードは表示されません。</p>

    <label>
        <span>PDF URL</span>
        <input type="url" name="_gwob_pdf_url" id="gwob_pdf_url" value="<?php echo esc_attr($values['_gwob_pdf_url']); ?>" class="widefat">
    </label>
    <button type="button" class="button" id="gwob_select_pdf">メディアライブラリからPDFを選択</button>

    <label>
        <span>Free PDF to Flipbook ショートコード</span>
        <input type="text" name="_gwob_flipbook_shortcode" id="gwob_flipbook_shortcode" value="<?php echo esc_attr($values['_gwob_flipbook_shortcode']); ?>" class="widefat" placeholder="[fptf-flipbook id=&quot;123&quot;]">
    </label>

    <label>
        <span>サブタイトル</span>
        <input type="text" name="_gwob_subtitle" value="<?php echo esc_attr($values['_gwob_subtitle']); ?>" class="widefat">
    </label>

    <label>
        <span>著者</span>
        <input type="text" name="_gwob_author" value="<?php echo esc_attr($values['_gwob_author']); ?>" class="widefat">
    </label>

    <label>
        <span>ページ数</span>
        <input type="number" min="0" name="_gwob_page_count" value="<?php echo esc_attr($values['_gwob_page_count']); ?>" class="small-text">
    </label>

    <label>
        <span>公開範囲</span>
        <select name="_gwob_access_level">
            <option value="public" <?php selected($values['_gwob_access_level'], 'public'); ?>>誰でも閲覧可</option>
            <option value="logged_in" <?php selected($values['_gwob_access_level'], 'logged_in'); ?>>ログインユーザーのみ</option>
            <option value="private" <?php selected($values['_gwob_access_level'], 'private'); ?>>管理者・編集者のみ</option>
        </select>
    </label>

    <label>
        <span>並び順</span>
        <input type="number" min="0" name="_gwob_order" value="<?php echo esc_attr($values['_gwob_order']); ?>" class="small-text">
    </label>

    <label class="gwob-checkbox">
        <input type="checkbox" name="_gwob_featured" value="1" <?php checked($values['_gwob_featured']); ?>>
        おすすめ表示
    </label>

    <label class="gwob-checkbox">
        <input type="checkbox" name="_gwob_allow_public_share" value="1" <?php checked($values['_gwob_allow_public_share']); ?>>
        外部公開を許可
    </label>
</div>
