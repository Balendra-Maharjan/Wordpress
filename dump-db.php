<?php
require_once __DIR__ . '/wp-load.php';

$pages = get_posts([
    'post_type' => 'page',
    'posts_per_page' => -1,
    'post_status' => 'any',
]);

$output = "PAGES TEMPLATES:\n";
foreach ($pages as $p) {
    $template = get_post_meta($p->ID, '_wp_page_template', true);
    $output .= "- ID: {$p->ID}, Slug: {$p->post_name}, Title: {$p->post_title}, Template: {$template}\n";
}

$blogs = get_posts([
    'post_type' => 'blog',
    'posts_per_page' => -1,
    'post_status' => 'any',
]);
$output .= "\nTOTAL BLOGS: " . count($blogs) . "\n";

file_put_contents(__DIR__ . '/db-output.txt', $output);
echo "Done\n";
