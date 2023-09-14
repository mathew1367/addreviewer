<?php

/**

 * Plugin Name: Custom Product Review

 * Description: Add a custom meta box in WooCommerce product edit page to add fake reviews.

 * Version: 1.0.0

 * Author: Mathew 

 * Author URI:   https://seosmile.ir

 */



// Add meta box in product edit page

function cwpai_add_product_review_meta_box() {

    add_meta_box(

        'cwpai_product_review_meta_box',

        'Product Review',

        'cwpai_render_product_review_meta_box',

        'product',

        'side',

        'default'

    );

}

add_action('add_meta_boxes', 'cwpai_add_product_review_meta_box');


// Render product review meta box content
function cwpai_render_product_review_meta_box($post) {
    wp_nonce_field('cwpai_product_review_meta_box', 'cwpai_product_review_nonce');
    
    echo '<label for="cwpai_username">Username:</label> ';
    echo '<input type="text" id="cwpai_username" name="cwpai_username" value="" />';
    echo '<br>';
    
    echo '<label for="cwpai_comment">Comment:</label> ';
    echo '<textarea id="cwpai_comment" name="cwpai_comment"></textarea>';
    echo '<br>';
    
    echo '<label for="cwpai_rating">Rating:</label> ';
    echo '<input type="number" id="cwpai_rating" name="cwpai_rating" min="1" max="5" />';
    echo '<br>';

    // Add a submit button for the review
    echo '<input type="submit" name="cwpai_submit_review" value="Submit Review" />';
}

// Save product review meta box data
function cwpai_save_product_review_meta_box($post_id) {
    if (!isset($_POST['cwpai_product_review_nonce']) || !wp_verify_nonce($_POST['cwpai_product_review_nonce'], 'cwpai_product_review_meta_box')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (!isset($_POST['cwpai_username']) || !isset($_POST['cwpai_comment']) || !isset($_POST['cwpai_rating'])) {
        return;
    }

    // Check if the review submit button was pressed
    if (!isset($_POST['cwpai_submit_review'])) {
        return;
    }
    
    $username = sanitize_text_field($_POST['cwpai_username']);
    $comment = sanitize_textarea_field($_POST['cwpai_comment']);
    $rating = intval($_POST['cwpai_rating']);
    
    $review_data = array(
        'comment_post_ID' => $post_id,
        'comment_author' => $username,
        'comment_content' => $comment,
        'comment_type' => 'review',
        'comment_approved' => 1,
        'comment_rating' => $rating
    );
    
    wp_insert_comment($review_data);
}


add_action('save_post_product', 'cwpai_save_product_review_meta_box');



// Only allow admin users to see and use this feature

function cwpai_restrict_product_review_access() {

    if (!current_user_can('administrator')) {

        wp_die('You do not have permission to access this feature.');

    }

}

add_action('admin_init', 'cwpai_restrict_product_review_access');
