<?php

/***
 * Plugin Name: Treatmentf Client Portal
 * Description: Treatment First client portal integration into your wordpress website, by clicking just one button
 * Theme URI: https://github.com/MuhammadUsman0304/treatmeant-client-portal
 * Author: Muhammad Usman
 * Author URI: https://www.linkedin.com/in/muhammad-usman-b3439218b/
 * Version: 1.0.0
 */

defined('ABSPATH') || die("hey, you can't call me directly");



function treatmentf_iframe_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'src' => 'https://treatmentf.herokuapp.com/BP/clientLogin/',
    ), $atts);
    return '<iframe src="' . esc_url($atts['src']) . '" width="100%" height="600"></iframe>';
}
add_shortcode('addiframe', 'treatmentf_iframe_shortcode');

function treatmentf_the_content_filter($content)
{
    global $post;
    $treatmentf_iframe_field = get_post_meta($post->ID, '_treatmentf_iframe_field', true);
    if (!empty($treatmentf_iframe_field)) {
        $content = $content . do_shortcode($treatmentf_iframe_field);
    }
    return $content;
}
add_filter('the_content', 'treatmentf_the_content_filter');

// Add the buttons to the page
add_filter('manage_pages_columns', 'treatmentf_add_portal_button_column');
add_action('manage_pages_custom_column', 'treatmentf_add_portal_button_column_content', 10, 2);

function treatmentf_add_portal_button_column($columns)
{
    $columns['portal_button'] = 'TreatmeantF Portal Integration';
    return $columns;
}

function treatmentf_add_portal_button_column_content($column_name, $post_id)
{
    if ($column_name == 'portal_button') {
        echo '<button class="insert-shortcode" data-page-id="' . $post_id . '">Activate Portal</button>';
        echo '<button class="remove-shortcode" data-page-id="' . $post_id . '">Deactivate Portal</button>';
    }
}


add_action('admin_enqueue_scripts', 'treatmentf_portal_enqueue_scripts');
add_action('wp_enqueue_scripts', 'treatmentf_portal_enqueue_scripts');

function treatmentf_portal_enqueue_scripts()
{
    wp_enqueue_script('treatmentf-portal-ajax-script', plugin_dir_url(__FILE__) . 'treatmentf-portal-ajax-script.js', array('jquery'), '1.0', true);
    wp_localize_script('treatmentf-portal-ajax-script', 'myAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('my-nonce')
    ));
}


add_action('wp_ajax_treatmentf_insert_shortcode', 'treatmentf_portal_insert_shortcode');

function treatmentf_portal_insert_shortcode()
{

    // Verify the nonce
    if (!wp_verify_nonce($_POST['nonce'], 'my-nonce')) {
        wp_send_json_error('Invalid nonce');
        exit;
    }

    // Get the page ID and shortcode from the POST data
    $page_id = $_POST['page_id'];
    $shortcode = '[addiframe]';

    // Get the current page content
    $page = get_post($page_id);
    $content = $page->post_content;

    // Insert the shortcode into the content
    $new_content = $content . $shortcode;

    // Update the page content
    wp_update_post(array(
        'ID' => $page_id,
        'post_content' => $new_content
    ));

    // Return a success response
    wp_send_json_success();
}



add_action('wp_ajax_treatmentf_remove_shortcode', 'treatmentf_portal_remove_shortcode');
function treatmentf_portal_remove_shortcode()
{
    $page_id = intval($_POST['page_id']);
    $nonce = $_POST['nonce'];

    if (!wp_verify_nonce($nonce, 'my-nonce')) {
        wp_die('Invalid nonce');
    }

    $content = get_post_field('post_content', $page_id);
    $new_content = str_replace('[addiframe]', '', $content);
    wp_update_post(array(
        'ID' => $page_id,
        'post_content' => $new_content
    ));

    wp_send_json_success('treatment first portal deactivated successfully');
}
