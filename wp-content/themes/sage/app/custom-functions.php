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

add_action('wp_ajax_filter_and_paginate', 'sage_filter_and_paginate_handler');
add_action('wp_ajax_nopriv_filter_and_paginate', 'sage_filter_and_paginate_handler');

function sage_filter_and_paginate_handler() {
    check_ajax_referer('filter_nonce', 'nonce');

    $search_query = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $category_slug = isset($_POST['category_slug']) ? sanitize_text_field($_POST['category_slug']) : '';
    $paged = isset($_POST['paged']) ? (int)$_POST['paged'] : 1;

    error_log('Received category_slug: ' . $category_slug); // Debugging line

    $args = [
        'post_type' => 'post',
        'posts_per_page' => 6, // Set to 6 posts per page
        's' => $search_query,
        'category_name' => $category_slug, // Changed to category_name
        'paged' => $paged,
    ];

    error_log('WP_Query args: ' . print_r($args, true)); // Debugging line

    $query = new WP_Query($args);

    $response = [
        'html' => '',
        'pagination' => '', // New key for pagination HTML
        'max_num_pages' => 0,
        'query_args' => $args, // Add query arguments for debugging
    ];

    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            // Render post HTML (similar to template-filter.blade.php's article structure)
            echo '<article>';
            echo '<h2><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h2>';
            echo '<div class="entry-summary">' . get_the_excerpt() . '</div>';
            echo '</article>';
        }
    } else {
        echo '<p>No posts found.</p>';
    }

    $response['html'] = ob_get_clean();
    $response['max_num_pages'] = $query->max_num_pages;

    // Generate pagination links
    $pagination_args = [
        'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
        'total' => $query->max_num_pages,
        'current' => max(1, $paged),
        'format' => '?paged=%#%',
        'prev_text' => __('&laquo; Previous'),
        'next_text' => __('Next &raquo;'),
        'type' => 'array', // Return as array to handle in JS
    ];

    // Add search and category to pagination links
    if (!empty($search_query)) {
        $pagination_args['add_args']['search'] = urlencode($search_query);
    }
    if (!empty($category_id)) {
        $pagination_args['add_args']['category'] = $category_id;
    }

    $pagination_links = paginate_links($pagination_args);

    // Convert array of links to HTML string
    if (is_array($pagination_links)) {
        $response['pagination'] = '<div class="pagination-links">' . implode('', $pagination_links) . '</div>';
    }


    wp_reset_postdata();

    wp_send_json_success($response);

    wp_die();
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
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'blogs'],
        'show_in_rest'       => true, // for Gutenberg and REST API
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
        'menu_icon'          => 'dashicons-portfolio', // WordPress icon class
    ];

    register_post_type('blog', $args);
}
add_action('init', 'register_custom_post_type');

add_action('wp_ajax_load_more_projects', 'load_more_projects');
add_action('wp_ajax_nopriv_load_more_projects', 'load_more_projects');

function load_more_projects() {
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $args = [
        'post_type' => 'blog',
        'posts_per_page' => 2,
        'paged' => $paged,
    ];

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post(); ?>
            <article>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="excerpt"><?php the_excerpt(); ?></div>
            </article>
        <?php endwhile;
    endif;
    wp_reset_postdata();

    $html = ob_get_clean();
    $has_more = $query->max_num_pages > $paged;

    wp_send_json_success([
        'html' => $html,
        'has_more' => $has_more,
    ]);
}


add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_home() && $query->is_main_query()) {
        $query->set('post_type', 'blog');
        $query->set('posts_per_page', 4);
    }
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('sage/filter.js', \Roots\asset('resources/js/filter.js')->uri(), ['jquery'], null, true);
    wp_localize_script('sage/filter.js', 'filter_params', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('filter_nonce'),
    ]);
});


