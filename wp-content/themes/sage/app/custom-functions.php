<?php

/* ===================================================
function to safelist blocks and remove unwanted blocks
==================================================== */
add_filter('allowed_block_types_all', 'sage_allowed_block_types', 10, 2);
function sage_allowed_block_types($blockEditorContent, $editorContent) {
    $blockRegistry = WP_Block_Type_Registry::get_instance();
    $allBlocks = $blockRegistry->get_all_registered();
    $allowBlocks = [
        'core/paragraph',
        'core/heading',
        'core/list-item',
        'core/list',
        'core/image',
        'core/freeform',
        'core/video',
        'core/embed',
        'core/shortcode',
        'core/quote'
    ];

    foreach ($allBlocks as $blockName => $blockType) {
        if (strpos($blockName, 'acf/') === 0) {
            $allowBlocks[] = $blockName;
        }
    }

    if (! empty($editorContent->post)) {
        return $allowBlocks;
    }

    return $blockEditorContent;
}

// Enqueue Google Fonts. Edit the src below to add your own fonts
function sage_enqueue_google_fonts() {
    wp_enqueue_style(
        'sage-google-fonts',
        'https://fonts.googleapis.com/css2?family=Gelasio&family=Plus+Jakarta+Sans:ital,wght@0,400..700;1,400..700&display=swap',
        [],
        null
    );
}
add_action('wp_enqueue_scripts', 'sage_enqueue_google_fonts');
add_action('enqueue_block_editor_assets', 'sage_enqueue_google_fonts');


// Bootstrap is already installed/enqueued elsewhere per user request.

// Ensure Bootstrap is present: enqueue CDN fallback only if not already registered/enqueued
add_action('wp_enqueue_scripts', function () {
    $has_bootstrap_css = wp_style_is('bootstrap', 'enqueued') || wp_style_is('bootstrap', 'registered') || wp_style_is('bootstrap-5', 'enqueued') || wp_style_is('bootstrap-5', 'registered');
    $has_bootstrap_js  = wp_script_is('bootstrap', 'enqueued') || wp_script_is('bootstrap', 'registered') || wp_script_is('bootstrap-5-bundle', 'enqueued') || wp_script_is('bootstrap-5-bundle', 'registered');

    if (! $has_bootstrap_css) {
        wp_enqueue_style('bootstrap-5', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '5.3.3');
    }
    if (! $has_bootstrap_js) {
        wp_enqueue_script('bootstrap-5-bundle', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], '5.3.3', true);
    }
}, 20);


// ACF > WYSIWYG — Custom Toolbar
add_filter('acf/fields/wysiwyg/toolbars', function ($toolbars) {
    $toolbars['Basic'][1] = [
        'removeformat',
        'alignleft',
        'aligncenter',
        'alignright',
        'bold',
        'italic',
        'underline',
        'link',
        'bullist',
        'numlist'

    ];
    $toolbars['Limited'][1] = [
        'bold',
        'italic',
        'underline',
        'link',
    ];
    return $toolbars;
});

//add svg and webp to allowed file uploads
function sage_add_file_types_to_uploads($file_types){

    $new_filetypes = array();
    $new_filetypes['svg'] = 'image/svg+xml';
    $new_filetypes['webp'] = 'image/webp';
    $file_types = array_merge($file_types, $new_filetypes );

    return $file_types;
}
add_action('upload_mimes', 'sage_add_file_types_to_uploads');


// Function to render inline SVG
function sage_render_inline_svg( $url ) {
    $svgFile = get_attached_file( attachment_url_to_postid( $url ) );
    if( file_exists( $svgFile ) ) {
        return file_get_contents( $svgFile );
    }
    return $url;
}

// Slugify any provided string
function sage_slugify( $string ) {
    if( empty( $string ) ) {
        return false;
    }
    $slug = strtolower( trim ( preg_replace('/[^A-Za-z0-9-]+/', '-', $string ) ) );
    return $slug;
}

// Get image data from ACF or WP
function sage_get_image_data( $image_data, $source_type = 'acf', $loading='lazy', $fetch_priority='high',$imageSizes = false ) {
    if ($source_type === 'acf') {
        // Handle ACF image field
        if (!is_array($image_data) || !isset($image_data['sizes']) || !isset($image_data['url'])) {
            return false;
        }
        $sizes = $image_data['sizes'];
        if($imageSizes) {
            $width = $image_data['width'];
            $height = $image_data['height'];
        }

        $src = $image_data['url'];
        // First try to get alt, then title, then filename as fallback
        $alt = $image_data['alt'] ?: $image_data['title'] ?: basename($image_data['url']);
        //For Caption
        // $caption = $image_data['caption'] ?? false;
    } elseif ($source_type === 'wp' && is_numeric($image_data)) {
        // Handle WordPress media attachment
        $image_meta = wp_get_attachment_metadata($image_data);

        if (!$image_meta || !isset($image_meta['sizes']) || !isset($image_meta['file'])) {
            return false;
        }
        if($imageSizes) {
            $width= $image_meta['width'];
            $height= $image_meta['height'];
        }
        $uploads_dir = wp_upload_dir();
        $base_url = $uploads_dir['baseurl'] . '/' . dirname($image_meta['file']);
        $src = wp_get_attachment_url($image_data);

        // Get alt text, fallback to post title, then filename if neither exists
        $alt = get_post_meta($image_data, '_wp_attachment_image_alt', true);
        if (empty($alt)) {
            $attachment = get_post($image_data);
            $alt = $attachment->post_title ?: basename($src);
        }
        // For Caption
        // $caption = wp_get_attachment_caption($image_data) ?? false;
        $sizes = $image_meta['sizes'];
    } else {
        return false; // Invalid source type or data
    }

    $srcset = [];
    $allowed_sizes = [
        'medium'       => 300,
        'medium_large' => 768,
        'large'        => 1024,
    ];

    // Loop through allowed sizes and build the srcset array
    foreach ($allowed_sizes as $size => $max_width) {
        if (isset($sizes[$size])) {
            if ($source_type === 'wp') {
                $size_url = wp_get_attachment_image_src($image_data, $size);
                if ($size_url) {
                    $url = $size_url[0];
                    $width = $size_url[1];
                    $srcset[] = "{$url} {$width}w";
                }
            } else {
                // ACF handling
                $url = $sizes[$size];
                $width = $sizes["{$size}-width"] ?? $max_width;
                if (is_string($url)) {
                    $srcset[] = "{$url} {$width}w";
                }
            }
        }
    }

    $full_width = $source_type === 'wp' ? $image_meta['width'] : ($image_data['width'] ?? 1500);
    $srcset[] = "{$src} {$full_width}w";

    if (empty($srcset)) {
        return false;
    }
    $srcset_string = implode(', ', $srcset);

    $return_array = [
        'imageSrc'      => esc_url($src),
        'imageSrcSet'   => esc_attr($srcset_string),
        'imageAlt'      => esc_attr($alt),
        'loading'       => $loading, //lazy, eager
        'sizes'         => '100vw', // string need to be passed
    ];
    if($imageSizes) {
        $return_array['width'] = $width;
        $return_array['height'] = $height;
    }

    if( $loading == 'eager' ) {
        $return_array['fetchPriority'] = $fetch_priority;
    }
    return $return_array;
}

// Calculate section gap based on top and bottom gap. Feel free to change the conditions
function sage_section_gap($sectionTopGap, $sectionBottomGap) {
    switch($sectionTopGap){
        case 'none':
            $sectionTopGap = 'pt-0';
            break;
        case 'xsmall':
            $sectionTopGap = 'pt-6';
            break;
        case 'small':
            $sectionTopGap = 'pt-10';
            break;
        case 'medium':
            $sectionTopGap = 'pt-16 pt-md-20';
            break;
        case 'large':
            $sectionTopGap = 'pt-20 pt-md-28';
            break;
    }

    switch($sectionBottomGap){
        case 'none':
            $sectionBottomGap = 'pb-0 ';
            break;
        case 'xsmall':
            $sectionBottomGap = 'pb-6 ';
            break;
        case 'small':
            $sectionBottomGap = 'pb-10 ';
            break;
        case 'medium':
            $sectionBottomGap = 'pb-16 pb-md-20 ';
            break;
        case 'large':
            $sectionBottomGap = 'pb-20 pb-md-28 ';
            break;

    }

    $sectionGap = $sectionTopGap . ' ' . $sectionBottomGap;

    return $sectionGap;
}

function sage_custom_pagination($query, $paged) {
    $total_pages = $query->max_num_pages;
    $current_page = max(1, intval($paged));

    if ($total_pages > 1) {
        $pagination_html = '<nav class="pagination-container mt-8 flex justify-center" aria-label="Pagination">';
        $pagination_html .= '<div class="pagination-links inline-flex -space-x-px rounded-md shadow-sm text-sm">';

        // Previous Page Link
        if ($current_page > 1) {
            $pagination_html .= '<a href="?paged=' . ($current_page - 1) . '" class="pagination-link relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white font-medium text-gray-500 hover:bg-gray-50 transition" aria-label="Previous Page">&laquo; Prev</a>';
        } else {
            $pagination_html .= '<span class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-gray-100 font-medium text-gray-400 cursor-not-allowed">&laquo; Prev</span>';
        }

        // Build pages array using sliding window logic
        $raw_pages = [];

        if ($total_pages <= 10) {
            $raw_pages = range(1, $total_pages);
        } else {
            // Near start: show pages 1-5 and bridge pages 6-8, then last 5
            if ($current_page <= 5) {
                for ($i = 1; $i <= 8; $i++) {
                    $raw_pages[] = $i;
                }
                for ($i = $total_pages - 4; $i <= $total_pages; $i++) {
                    $raw_pages[] = $i;
                }
            } 
            // Near end: show first 5, then bridge pages, then last 5
            elseif ($current_page >= $total_pages - 4) {
                for ($i = 1; $i <= 5; $i++) {
                    $raw_pages[] = $i;
                }
                for ($i = $total_pages - 7; $i <= $total_pages; $i++) {
                    $raw_pages[] = $i;
                }
            } 
            // Middle case: show first 5, sliding window, last 5
            else {
                for ($i = 1; $i <= 5; $i++) {
                    $raw_pages[] = $i;
                }
                for ($i = $current_page - 2; $i <= $current_page + 2; $i++) {
                    $raw_pages[] = $i;
                }
                for ($i = $total_pages - 4; $i <= $total_pages; $i++) {
                    $raw_pages[] = $i;
                }
            }
        }

        // Sort and de-duplicate page numbers
        $raw_pages = array_unique($raw_pages);
        sort($raw_pages);

        // Build final navigation items with ellipses
        $pagination_items = [];
        $prev = 0;

        foreach ($raw_pages as $page) {
            if ($prev > 0) {
                $gap = $page - $prev;
                if ($gap == 2) {
                    $pagination_items[] = $prev + 1; // fill single page gap
                } elseif ($gap > 2) {
                    $pagination_items[] = '...'; // add ellipsis for larger gaps
                }
            }
            $pagination_items[] = $page;
            $prev = $page;
        }

        // Render buttons
        foreach ($pagination_items as $item) {
            if ($item === '...') {
                $pagination_html .= '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-500 cursor-default">...</span>';
            } elseif ($item == $current_page) {
                $pagination_html .= '<span class="current relative inline-flex items-center px-4 py-2 border border-blue-600 bg-blue-600 font-semibold text-white z-10">' . $item . '</span>';
            } else {
                $pagination_html .= '<a href="?paged=' . $item . '" class="pagination-link relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white font-medium text-gray-700 hover:bg-gray-50 transition">' . $item . '</a>';
            }
        }

        // Next Page Link
        if ($current_page < $total_pages) {
            $pagination_html .= '<a href="?paged=' . ($current_page + 1) . '" class="pagination-link relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white font-medium text-gray-500 hover:bg-gray-50 transition" aria-label="Next Page">Next &raquo;</a>';
        } else {
            $pagination_html .= '<span class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-gray-100 font-medium text-gray-400 cursor-not-allowed">Next &raquo;</span>';
        }

        $pagination_html .= '</div>';
        $pagination_html .= '</nav>';

        return $pagination_html;
    }
    return '';
}

add_action('wp_ajax_filter_and_paginate', 'sage_filter_and_paginate_handler');
add_action('wp_ajax_nopriv_filter_and_paginate', 'sage_filter_and_paginate_handler');

function sage_filter_and_paginate_handler() {
    check_ajax_referer('filter_nonce', 'nonce');

    $search_query = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $category_slug = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $paged = isset($_POST['paged']) ? (int)$_POST['paged'] : 1;
    $posts_per_page = get_field('no_of_post_per_page', 'option') ?: 6;

    $args = [
        'post_type' => 'blog',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        's' => $search_query,
    ];

    if ($category_slug) {
        $args['tax_query'][] = [
            'taxonomy' => 'category',
            'field' => 'slug',
            'terms' => $category_slug,
        ];
    }

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        $counter = 0;
        while ($query->have_posts()) {
            $query->the_post();
            $counter++;

            echo '<article>';
            if (has_post_thumbnail()) {
                echo '<div class="post-thumbnail">';
                if ($counter === 1 && $paged === 1) {
                    echo get_the_post_thumbnail(get_the_ID(), 'medium', [
                        'loading' => 'eager',
                        'fetchpriority' => 'high'
                    ]);
                } else {
                    echo get_the_post_thumbnail(get_the_ID(), 'medium', [
                        'loading' => 'lazy'
                    ]);
                }
                echo '</div>';
            }
            echo '<h2><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h2>';
            echo '<div class="entry-summary">' . get_the_excerpt() . '</div>';
            echo '</article>';
        }
    } else {
        echo '<p class="col-12 text-center">No posts found.</p>';
    }

    $html = ob_get_clean();

    wp_send_json_success([
        'html' => $html,
        'pagination' => sage_custom_pagination($query, $paged),
        'max_num_pages' => $query->max_num_pages
    ]);

    wp_reset_postdata();
}

function register_custom_post_type() {

    $labels = [
        'name'               => 'blogs',
        'singular_name'      => 'blog',
        'menu_name'          => 'blogs',
        'name_admin_bar'     => 'blog',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New blog',
        'new_item'           => 'New blog',
        'edit_item'          => 'Edit blog',
        'view_item'          => 'View blog',
        'all_items'          => 'All blogs',
        'search_items'       => 'Search blogs',
        'parent_item_colon'  => 'Parent blogs:',
        'not_found'          => 'No blogs found.',
        'not_found_in_trash' => 'No blogs found in Trash.'
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'rewrite'            => ['slug' => '', 'with_front' => false],
        'show_in_rest'       => true, // for Gutenberg and REST API
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
        'menu_icon'          => 'dashicons-portfolio', // WordPress icon class
        'taxonomies'         => ['category'],
        'show_ui'            => true, 
        'show_in_menu'       => true, 
    ];

    register_post_type('blog', $args);
}
add_action('init', 'register_custom_post_type');

function load_more_projects() {
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $args = [
        'post_type' => 'blog',
        'posts_per_page' => 2,
        'paged' => $paged,
    ];

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        $counter = 0;
        while ($query->have_posts()) {
            $query->the_post();
            $counter++;
            echo \Roots\view('partials.content-blog', ['index' => $counter - 1, 'paged' => $paged])->render();
        }
    }

    $html = ob_get_clean();
    wp_reset_postdata();

    wp_send_json_success([
        'html' => $html,
        'has_more' => ($query->max_num_pages > $paged)
    ]);
}

add_action('wp_ajax_load_more_projects', 'load_more_projects');
add_action('wp_ajax_nopriv_load_more_projects', 'load_more_projects');



add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_home() && $query->is_main_query()) {
        $query->set('post_type', 'blog');
        $query->set('posts_per_page', 4);
    }
});

add_action('wp_enqueue_scripts', function () {
    $params = [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('filter_nonce'),
    ];

    wp_enqueue_script('sage/filter.js', \Roots\asset('resources/js/filter.js')->uri(), ['jquery'], null, true);
    wp_localize_script('sage/filter.js', 'filter_params', $params);

    wp_enqueue_script('sage/infinite-scroll.js', \Roots\asset('resources/js/infinite-scroll.js')->uri(), [], null, true);
    wp_localize_script('sage/infinite-scroll.js', 'filter_params', $params);
});

// function add_html_to_cpt_permalink($post_link, $post) {
//     if ($post->post_type === 'blog') {
//         return home_url('/blogs/' . $post->post_name . '.html');
//     }
//     return $post_link;
// }
// add_filter('post_type_link', 'add_html_to_cpt_permalink', 10, 2);



function add_category_metabox_to_blog() {
    add_meta_box(
        'categorydiv',       // ID of the meta box
        'Categories',        // Title
        'post_categories_meta_box', // Callback function used by default posts
        'blog',              // Post type
        'side',              // Context
        'default'            // Priority
    );
}
add_action('add_meta_boxes', 'add_category_metabox_to_blog');

// Add custom columns
function blog_custom_columns($columns) {
    $columns['categories'] = 'Categories';
    return $columns;
}
add_filter('manage_blog_posts_columns', 'blog_custom_columns');

// Populate custom columns
function blog_custom_columns_content($column, $post_id) {
    switch ($column) {
        case 'categories':
            $terms = get_the_terms($post_id, 'category'); // default categories
            if (!empty($terms)) {
                $out = [];
                foreach ($terms as $term) {
                    $out[] = $term->name;
                }
                echo implode(', ', $out);
            }
            break;

        case 'thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, [50, 50]);
            }
            break;
    }
}
add_action('manage_blog_posts_custom_column', 'blog_custom_columns_content', 10, 2);

// Programmatically register the pagination-test page and seed mock posts if needed
add_action('init', function () {
    // 1. Check/Create the Page
    $page_slug = 'pagination-test';
    $page_title = 'Advanced Pagination';
    $page_check = get_page_by_path($page_slug);
    
    if (!isset($page_check->ID)) {
        $page_id = wp_insert_post([
            'post_title'    => $page_title,
            'post_content'  => 'Dynamic pagination test page.',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => $page_slug,
        ]);
        if ($page_id && !is_wp_error($page_id)) {
            update_post_meta($page_id, '_wp_page_template', 'template-pagination.blade.php');
        }
    } else {
        // Ensure the template is set correctly in case it was created with a default template
        update_post_meta($page_check->ID, '_wp_page_template', 'template-pagination.blade.php');
    }

    // 2. Check/Seed posts
    $blogs = get_posts([
        'post_type' => 'blog',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);

    $count = count($blogs);
    if ($count < 15) {
        $needed = 15 - $count;
        $reference_post = !empty($blogs) ? $blogs[0] : null;
        
        for ($i = 1; $i <= $needed; $i++) {
            $title = $reference_post 
                ? $reference_post->post_title . ' - Mock #' . $i
                : 'Sample Blog Post #' . ($count + $i);
            
            $content = $reference_post
                ? $reference_post->post_content
                : 'This is a sample blog post content created for testing advanced sliding pagination.';
            
            $new_post_id = wp_insert_post([
                'post_title'   => $title,
                'post_content' => $content,
                'post_status'  => 'publish',
                'post_type'    => 'blog',
            ]);

            if ($new_post_id && $reference_post) {
                // Category
                $categories = wp_get_post_categories($reference_post->ID);
                if (!empty($categories)) {
                    wp_set_post_categories($new_post_id, $categories);
                }
                
                // Featured image (thumbnail)
                $thumbnail_id = get_post_thumbnail_id($reference_post->ID);
                if ($thumbnail_id) {
                    set_post_thumbnail($new_post_id, $thumbnail_id);
                }
            }
        }
    }

    // 3. Debug Output to file
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
    $output .= "\nTOTAL BLOGS: " . count(get_posts(['post_type' => 'blog', 'posts_per_page' => -1])) . "\n";
    file_put_contents(ABSPATH . 'db-output.txt', $output);
});

// AJAX handler for template-pagination.blade.php
function sage_ajax_pagination_handler() {
    $paged = isset($_POST['paged']) ? (int)$_POST['paged'] : 1;
    $posts_per_page = isset($_POST['posts_per_page']) ? (int)$_POST['posts_per_page'] : 1;

    $args = [
        'post_type' => 'blog',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'post_status' => 'publish',
    ];

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        $counter = 0;
        while ($query->have_posts()) {
            $query->the_post();
            $counter++;
            echo \Roots\view('partials.content-blog', ['index' => $counter - 1, 'paged' => $paged])->render();
        }
    } else {
        echo '<p class="text-center py-10 text-gray-500">No blog posts found.</p>';
    }

    $html = ob_get_clean();
    wp_reset_postdata();

    wp_send_json_success([
        'html' => $html,
        'pagination' => sage_custom_pagination($query, $paged),
    ]);
}
add_action('wp_ajax_ajax_pagination', 'sage_ajax_pagination_handler');
add_action('wp_ajax_nopriv_ajax_pagination', 'sage_ajax_pagination_handler');

// Prevent WordPress from redirecting pagination query parameters on the pagination test page
add_filter('redirect_canonical', function ($redirect_url, $requested_url) {
    if (is_page('pagination-test') || is_page_template('template-pagination.blade.php')) {
        return false;
    }
    return $redirect_url;
}, 10, 2);
