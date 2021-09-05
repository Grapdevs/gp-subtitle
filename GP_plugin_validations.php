<?php 
/*
This file handles the post_title option on the bases of guttenburg and classic editor options

*/

/**
 * Get Subtitle by post ID.
 *  
 * @since 1.0.2
 * @param string  $post_id
 * @return mixed  returns subtitle if the post ID is null you still call this method, we will opt Global $post for this purpose
 * if you are inside wp_query loop or any loop where you are getting the post_id, then it's recomended to pass post_id
 */
 

 /**
 * Check if Block Editor is active.
 * @return bool
 */
function gp_is_classic_editor_plugin_active() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
        return true;
    }

    return false;
}