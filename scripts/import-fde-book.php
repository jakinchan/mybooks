<?php

$relative_file = 'incoming/fde-it-step-by-step-book.pdf';
$title = 'FDE IT Step by Step Book';
$slug = 'fde-it-step-by-step-book';
$subtitle = 'IT教材・手順書';

$uploads = wp_upload_dir();
$file_path = trailingslashit($uploads['basedir']) . $relative_file;
$file_url = trailingslashit($uploads['baseurl']) . $relative_file;

if (!file_exists($file_path)) {
    WP_CLI::error('PDF not found: ' . $file_path);
}

$attachment = get_posts([
    'post_type' => 'attachment',
    'name' => $slug,
    'post_status' => 'inherit',
    'numberposts' => 1,
    'fields' => 'ids',
]);

if ($attachment) {
    $attachment_id = (int) $attachment[0];
    wp_update_post([
        'ID' => $attachment_id,
        'post_title' => $title,
        'post_mime_type' => 'application/pdf',
        'guid' => $file_url,
    ]);
} else {
    $attachment_id = wp_insert_attachment([
        'post_title' => $title,
        'post_name' => $slug,
        'post_status' => 'inherit',
        'post_mime_type' => 'application/pdf',
        'guid' => $file_url,
    ], $file_path);
}

if (is_wp_error($attachment_id)) {
    WP_CLI::error($attachment_id->get_error_message());
}

update_post_meta((int) $attachment_id, '_wp_attached_file', $relative_file);

$term = term_exists('IT教材', 'gw_book_category');
if (!$term) {
    $term = wp_insert_term('IT教材', 'gw_book_category', ['slug' => 'it']);
}

$existing = get_posts([
    'post_type' => 'gw_book',
    'name' => $slug,
    'post_status' => 'any',
    'numberposts' => 1,
    'fields' => 'ids',
]);

$post_data = [
    'post_type' => 'gw_book',
    'post_status' => 'publish',
    'post_title' => $title,
    'post_name' => $slug,
    'post_excerpt' => 'IT教材・手順書のPDFです。',
    'post_content' => 'PDF資料を Free PDF to Flipbook のショートコードでページめくり式のオンライン本として表示します。',
];

if ($existing) {
    $post_id = (int) $existing[0];
    $post_data['ID'] = $post_id;
    $result = wp_update_post($post_data, true);
} else {
    $result = wp_insert_post($post_data, true);
    $post_id = (int) $result;
}

if (is_wp_error($result)) {
    WP_CLI::error($result->get_error_message());
}

wp_set_object_terms($post_id, ['it'], 'gw_book_category', false);

$meta = [
    '_gwob_pdf_url' => $file_url,
    '_gwob_flipbook_shortcode' => '[fptf-flipbook pdf="' . $file_url . '"]',
    '_gwob_subtitle' => $subtitle,
    '_gwob_author' => 'GoodWorld',
    '_gwob_page_count' => '',
    '_gwob_access_level' => 'public',
    '_gwob_order' => '2',
    '_gwob_featured' => '',
    '_gwob_allow_public_share' => '1',
];

foreach ($meta as $key => $value) {
    update_post_meta($post_id, $key, $value);
}

WP_CLI::success('Book ready: ' . get_permalink($post_id));
WP_CLI::line('PDF: ' . $file_url);
