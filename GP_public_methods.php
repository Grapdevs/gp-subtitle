<?php 
/*
This files handles Public methods and shortcodes

*/

/**
 * Get Subtitle by post ID.
 *  
 * @since 1.0.2
 * @param string  $post_id
 * @return mixed  returns subtitle if the post ID is null you still call this method, we will opt Global $post for this purpose
 * if you are inside wp_query loop or any loop where you are getting the post_id, then it's recomended to pass post_id
 */
 
function gp_get_subtitle( $post_id = null )
{
    if($post_id == null)
    {
        global $post;
        $post_id = $post->ID;
    }
    $subtitle = (get_post_meta( $post_id, 'subtitle', true ));
    return $subtitle;
}



/**
 * Get Subtitle using shortcode.
 * Example:
 * Example 1: [gp_subtitle id='Your Post ID'] or 
 * Example 1: [gp_subtitle] no need to pass ID, we will use global $post.
 * @since 1.0.2
 * @param string  $post_id
 * 
 */

add_shortcode( 'gp_subtitle', 'gp_subtitle_shortcode' );
function gp_subtitle_shortcode( $atts = null ) {

    $atts = shortcode_atts( array(
        'id' => '',
    ), $atts, 'gp_subtitle' );

    return gp_get_subtitle($atts['id']);
}