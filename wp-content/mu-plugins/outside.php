<?php
/*
Plugin Name: Outside Security Implementation
Plugin URI: https://otuside.tech
Description: Outside Security Implementation
Version: 1.0
Author: Outside
Author URI: http://Outside.tech
License: GPLv2 or later
*/ 
class Outside_Security_Implementation
{
    public function __construct() {
        $this->add_actions();
        $this->add_filters();
        $this->remove_actions();
    }
    
    public function add_actions() {
        add_action( 'admin_init',  array($this, 'outside_security_fields') );

        // add_action( 'add_meta_boxes', array($this, 'meta_init') );
        add_action( 'admin_menu', array($this,'outside_security_page') );

        add_action( 'admin_notices', array($this, 'outside_security_notice') );
        $value = get_option( 'remove_rest_api_filter' );
        if( $value == 'yes') {
            add_action(
                'rest_api_init',
                function() {
                    remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
                    add_filter( 'rest_pre_serve_request', array($this, 'initCors') );
                }
            );
        }
        $value = get_option( 'remove_brute_redirect_filter' );
        if( $value == 'yes' ) {
            add_action( 'template_redirect', function() {
                if (preg_match('/author=([0-9]*)/i', $_SERVER['QUERY_STRING']))
                    wp_safe_redirect(site_url().'/404');//die();
                if(preg_match('/rest_route=([0-9]*)/i', $_SERVER['QUERY_STRING']))
                    wp_safe_redirect(site_url().'/404');//die();
                if (strpos($_SERVER['REQUEST_URI'], "wp-json/wp/v2/users")!==false)
                    wp_safe_redirect(site_url().'/404');//die();
            });
        }
    }

    public function remove_actions() {
        $value = get_option( 'remove_unwanted_svg_filter' );
        
        if( $value == 'yes') {
            remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
            remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
        }
        $value = get_option( 'remove_duotone_filter' );
        if( $value == 'yes' )
            remove_filter( 'render_block', 'wp_render_duotone_support', 10);
        
    }

    public function add_filters() {
        
        $value = get_option( 'security_active' );
        if( $value == 'yes' ) {
            add_filter( 'wp_headers', array($this, 'additional_securityheaders') );
        }

        $value = get_option('remove_jquery_filter');
        if( $value == 'yes')
            add_filter( 'wp_enqueue_scripts', array($this, 'jquery_removal'), PHP_INT_MAX );

        $value = get_option( 'remove_brute_redirect_filter' );
        if ($value == 'yes') {
            add_filter('redirect_canonical', array($this, 'outside_brute_redirect'), 10, 2);
        }
    }
    public function additional_securityheaders() {
        
        if ( ! is_admin() ) {
            $value = get_option( 'security_additional_headers' );
            $headers['Referrer-Policy']             = 'no-referrer-when-downgrade'; //This is the default value, the same as if it were not set.
            $headers['X-Content-Type-Options']      = 'nosniff';
            $headers['X-XSS-Protection']            = '1; mode=block';
            $headers['Content-Security-Policy']     = $value;
            //$headers['Strict-Transport-Security']   = "max-age=31536000; includeSubDomains";
            $headers['X-Frame-Options']             = 'DENY';
            $headers['cache-control']               = 'public, max-age=2592000, immutable'; //'max-age=2592000';
            $headers['cross-origin-resource-policy']= 'same-origin';
            $headers['Access-Control-Allow-Origin'] = 'same-origin';
            $headers['Permissions-Policy'] = 'geolocation=(self "'.site_url().'"), microphone=()';
            return $headers;
        }
    }

    public function jquery_removal() {
        
        if( !is_user_logged_in()) {
            wp_dequeue_script( 'jquery');
            wp_deregister_script( 'jquery'); 
        }
    }
    public function outside_brute_redirect($redirect, $request) {
        if (preg_match('/\?author=([0-9]*)(\/*)/i', $request))
            wp_safe_redirect(site_url().'/404');//die();
        elseif (preg_match('/\?rest_route=([0-9]*)(\/*)/i', $request))
            wp_safe_redirect(site_url().'/404');//die();
        elseif (strpos($_SERVER['REQUEST_URI'], "wp-json/wp/v2/users")!==false)
            wp_safe_redirect(site_url().'/404');//die();
        else
            return $redirect;
    }

    public function outside_security_notice() {
        if(
            isset( $_GET[ 'page' ] ) 
            && 'outside_security' == $_GET[ 'page' ]
            && isset( $_GET[ 'settings-updated' ] ) 
            && true == $_GET[ 'settings-updated' ]
        ) {
            ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <strong>Settings settings saved.</strong>
                    </p>
                </div>
            <?php
        }
    
    }
    public function outside_security_fields() {
        $page_slug = 'outside_security';
        $option_group = 'outside_security_settings';

        // 1. create section
        add_settings_section(
            'outside_security_section_id', // section ID
            '', // title (optional)
            '', // callback function to display the section (optional)
            $page_slug
        );

        // 2. register fields
        register_setting( $option_group, 'security_active', array( $this, 'outside_security_sanitize_checkbox') );
        register_setting( $option_group, 'remove_unwanted_svg_filter', array( $this, 'outside_security_sanitize_checkbox') );
        register_setting( $option_group, 'remove_duotone_filter', array( $this, 'outside_security_sanitize_checkbox') );
        register_setting( $option_group, 'remove_jquery_filter', array( $this, 'outside_security_sanitize_checkbox') );
        register_setting( $option_group, 'remove_rest_api_filter', array( $this, 'outside_security_sanitize_checkbox') );
        register_setting( $option_group, 'remove_brute_redirect_filter', array( $this, 'outside_security_sanitize_checkbox') );
        register_setting( $option_group, 'security_additional_headers', array( $this, 'security_additional_headers_text') );
        // register_setting( $option_group, 'num_of_slides', 'absint' );

        // 3. add fields
        add_settings_field(
            'security_active',
            'Activate Security Options',
            array( $this, 'outside_security_checkbox'), // function to print the field
            $page_slug,
            'outside_security_section_id' // section ID
        );


        add_settings_field(
            'security_additional_headers',
            'Additional Security Headers',
            array( $this, 'security_additional_headers'), // function to print the field
            $page_slug,
            'outside_security_section_id' // section ID
        );

        add_settings_field(
            'remove_unwanted_svg_filter',
            'Remove Unwanted SVG Filter',
            array( $this, 'outside_unwanted_svg_checkbox'), // function to print the field
            $page_slug,
            'outside_security_section_id' // section ID
        );
        add_settings_field(
            'remove_duotone_filter',
            'Remove Duotone Filter',
            array( $this, 'outside_duotone_checkbox'), // function to print the field
            $page_slug,
            'outside_security_section_id' // section ID
        );
        add_settings_field(
            'remove_jquery_filter',
            'Remove Jquery from frontend',
            array( $this, 'outside_jquery_checkbox'), // function to print the field
            $page_slug,
            'outside_security_section_id' // section ID
        );
        add_settings_field(
            'remove_rest_api_filter',
            'Remove Rest API',
            array( $this, 'outside_rest_api_checkbox'), // function to print the field
            $page_slug,
            'outside_security_section_id' // section ID
        );
        add_settings_field(
            'remove_brute_redirect_filter',
            'Apply brute redirect to 404',
            array( $this, 'outside_brute_redirect_checkbox'), // function to print the field
            $page_slug,
            'outside_security_section_id' // section ID
        );

        // add_settings_field(
        //     'num_of_slides',
        //     'Number of slides',
        //     'rudr_number',
        //     $page_slug,
        //     'rudr_section_id',
        //     array(
        //         'label_for' => 'num_of_slides',
        //         'class' => 'hello', // for <tr> element
        //         'name' => 'num_of_slides' // pass any custom parameters
        //     )
        // );
    }

    /**
     * Checkbox
     */
    public function outside_security_checkbox( $args ) {
        $value = get_option( 'security_active' );
        ?>
            <label>
                <input type="checkbox" name="security_active" <?php checked( $value, 'yes' ) ?> /> Yes
            </label>
        <?php
    }
    public function outside_unwanted_svg_checkbox( $args ) {
        $value = get_option( 'remove_unwanted_svg_filter' );
        ?>
            <label>
                <input type="checkbox" name="remove_unwanted_svg_filter" <?php checked( $value, 'yes' ) ?> /> Yes
            </label>
        <?php
    }
    public function outside_duotone_checkbox( $args ) {
        $value = get_option( 'remove_duotone_filter' );
        ?>
            <label>
                <input type="checkbox" name="remove_duotone_filter" <?php checked( $value, 'yes' ) ?> /> Yes
            </label>
        <?php
    }
    public function outside_jquery_checkbox( $args ) {
        $value = get_option( 'remove_jquery_filter' );
        ?>
            <label>
                <input type="checkbox" name="remove_jquery_filter" <?php checked( $value, 'yes' ) ?> /> Yes
            </label>
        <?php
    }
    public function outside_rest_api_checkbox( $args ) {
        $value = get_option( 'remove_rest_api_filter' );
        ?>
            <label>
                <input type="checkbox" name="remove_rest_api_filter" <?php checked( $value, 'yes' ) ?> /> Yes
            </label>
        <?php
    }
    public function outside_brute_redirect_checkbox( $args ) {
        $value = get_option( 'remove_brute_redirect_filter' );
        ?>
            <label>
                <input type="checkbox" name="remove_brute_redirect_filter" <?php checked( $value, 'yes' ) ?> /> Yes
            </label>
        <?php
    }
    public function outside_security_sanitize_checkbox( $value ) {
        return 'on' === $value ? 'yes' : 'no';
    }

    /**
     * Textarea
     */
    public function security_additional_headers( $args ) {
        $value = get_option( 'security_additional_headers' );
        ?>
            <label>
                <textarea name="security_additional_headers" cols="100" rows="10"><?php echo $value;?></textarea>
            </label>
        <?php
    }

    public function initCors( $value ) {
        // $origin_url = '*';
        // // Check if production environment or not
        // if (ENVIRONMENT === 'production') {
        //   $origin_url = site_url();
        // }
        $origin_url = site_url();
        header( 'Access-Control-Allow-Origin: ' . $origin_url );
        header( 'Access-Control-Allow-Methods: GET' );
        header( 'Access-Control-Allow-Credentials: true' );
        return $value;
    }

    
    public function outside_security_page() {
        add_options_page(
            'Outside Security', // page <title>Title</title>
            'Security', // link text
            'manage_options', // user capabilities
            'outside_security', // page slug
            array($this,'outside_security_page_callback'), // this function prints the page content
            // 'dashicons-images-alt2', // icon (from Dashicons for example)
            0 // menu position
        );
    
    }
    public function outside_security_page_callback() {
        ?>
        <div class="wrap">
            <h1><?php echo get_admin_page_title() ?></h1>
            <form method="post" action="options.php">
                <?php
                    settings_fields( 'outside_security_settings' ); // settings group name
                    do_settings_sections( 'outside_security' ); // just a page slug
                    submit_button(); // "Save Changes" button
                ?>
            </form>
        </div>
        <?php

    }



}
new Outside_Security_Implementation();
