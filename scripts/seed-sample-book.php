<?php

$pdf_url = home_url('/wp-content/uploads/sample-goodworld-online-book.pdf');

$term = term_exists('サンプル', 'gw_book_category');
if (!$term) {
    $term = wp_insert_term('サンプル', 'gw_book_category', ['slug' => 'sample']);
}

$existing = get_posts([
    'post_type' => 'gw_book',
    'name' => 'sample-goodworld-online-book',
    'post_status' => 'any',
    'numberposts' => 1,
    'fields' => 'ids',
]);

if ($existing) {
    $post_id = (int) $existing[0];
    wp_update_post([
        'ID' => $post_id,
        'post_status' => 'publish',
        'post_title' => 'サンプルオンライン本',
        'post_excerpt' => 'SQLite 構成で PDF 本棚を確認するためのサンプルです。',
        'post_content' => 'このサンプル本は uploads に置いた PDF を GoodWorld Online Books で開きます。Free PDF to Flipbook のショートコードを使い、ページめくり式のオンライン本として表示します。',
    ]);
} else {
    $post_id = wp_insert_post([
        'post_type' => 'gw_book',
        'post_status' => 'publish',
        'post_title' => 'サンプルオンライン本',
        'post_name' => 'sample-goodworld-online-book',
        'post_excerpt' => 'SQLite 構成で PDF 本棚を確認するためのサンプルです。',
        'post_content' => 'このサンプル本は uploads に置いた PDF を GoodWorld Online Books で開きます。Free PDF to Flipbook のショートコードを使い、ページめくり式のオンライン本として表示します。',
    ], true);
}

if (is_wp_error($post_id)) {
    WP_CLI::error($post_id->get_error_message());
}

wp_set_object_terms((int) $post_id, ['sample'], 'gw_book_category', false);

$meta = [
    '_gwob_pdf_url' => $pdf_url,
    '_gwob_flipbook_shortcode' => '[fptf-flipbook pdf="' . $pdf_url . '"]',
    '_gwob_subtitle' => 'WordPress + SQLite 動作確認用',
    '_gwob_author' => 'GoodWorld',
    '_gwob_page_count' => '1',
    '_gwob_access_level' => 'public',
    '_gwob_order' => '1',
    '_gwob_featured' => '1',
    '_gwob_allow_public_share' => '1',
];

foreach ($meta as $key => $value) {
    update_post_meta((int) $post_id, $key, $value);
}

WP_CLI::success('Sample online book ready: ' . get_permalink((int) $post_id));
