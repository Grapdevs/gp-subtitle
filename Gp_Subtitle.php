<?php 
/*
/**
 * Gp Subtitle
 *
 * Plugin Name:  Gp Subtitle for Pages and Posts
 * Description: Enables the subtitle for pages and posts, you can easily manage the subtitle for pages or post.
 * Version:     1.0.1
 * Author:      Grapdevs
 * Author URI:  https://grapdevs.com/plugin-subtitle
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Author Email: contact@grapdevs.com 
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
 


if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

if ( ! class_exists( 'Gp_Subtitle' ) ) :
class Gp_Subtitle {

    private function __construct() {}

    private static function gp_get_subtitle( $post_id ){
       
        return (get_post_meta( $post_id, 'subtitle', true ));
    }

    /**
	* Creating of subtitle input field
	*/
    public static function gp_add_subtitle_with_form(){
        global $post;
        ?>
        <div class="gp-subtitle-input" id="titlediv">
            <input type="text" id="title"  name="subtitle" value="<?php echo esc_attr( self::gp_get_subtitle( $post->ID ))?>" placeholder="Your Subtitle Here" />
        </div>		
        <?php
    }

    /**
	* Method of saving sub-titles for pages and post
	*/
    public static function gp_save_subtitle(  $post_id ){
        if(!empty($post_id) && $post_id != null)
        {
            if( isset( $_POST[ 'subtitle' ] ) ) {
                update_post_meta( $post_id, 'subtitle' , wp_filter_nohtml_kses(trim(sanitize_text_field($_POST['subtitle']))));
            }
        }

    }
  
    /**
	* Priting the title for pages and posts for front-end side
	*/
    public static function gp_print_subtile_after_title($content){
        /**
	        * As you can choose either you want to show the subtitle on post or on page.
            * get_option('subtitle-allow-on-pages') == 1 , The reason behind on is because the setting filed value are one,
	    */

        if(( get_post_type() === 'page' && get_option('subtitle-allow-on-pages') == 1 ) || ( get_post_type() === 'post' && get_option('subtitle-allow-on-posts') == 1 )){
            global $post;
            $subtitle_content = self::gp_get_subtitle( $post->ID );
            $subtitle_content = '<h2 class="gp-subtitle">' . $subtitle_content . '</h2> ';
            $subtitle_content .= $content;
            return $subtitle_content;
        }
        return $content;
	} 

 
    /* Start -  Registration of admin page to handle the settings using WP-Setting APIs */

	public static function wp_register_settings() {
		// Add an option to Settings -> Writing
      
        register_setting( 'writing', 'subtitle-allow-on-posts', array(
			'sanitize_callback' => array( __CLASS__, 'gp_sanitize' ),
		) );

		register_setting( 'writing', 'subtitle-allow-on-pages', array(
			'sanitize_callback' => array( __CLASS__, 'gp_sanitize' ),
		) );

		$allowed_options = array(
			'writing' => array(
				'option-for-post',
				'option-for-page'
			),
		);

        $posts_field_heading = __( 'Allow Sub-Title for Posts (For Public view)', 'classic-editor' );
        $page_field_heading = __( 'Allow Sub-Title for Pages (For Public view)', 'classic-editor' );

		add_settings_field( 'setting-field-for-posts', $posts_field_heading, array( __CLASS__, 'gp_subtitle_settings_posts' ), 'writing' );
        add_settings_field( 'setting-field-for-pages', $page_field_heading, array( __CLASS__, 'gp_subtitle_settings_pages' ), 'writing' );
		
	}

    public static function gp_sanitize( $data ){
        return isset($data) ?  filter_var($data,FILTER_SANITIZE_NUMBER_INT) : false;
    }
    
      /* END -  Registration of admin page to handle the settings using WP-Setting APIs */
 
    public static function gp_register_assets()
    {
        wp_register_style('gp-subtitle', plugins_url('assets/gp-style.css',__FILE__ ),"", '1.0.1');
        wp_enqueue_style('gp-subtitle');
    }

    public static function gp_subtitle_settings_posts() {
		
		?>
		<input type="checkbox" value="1" <?php checked(get_option('subtitle-allow-on-posts', false)); ?> name="subtitle-allow-on-posts" > Yes
    
    <?php
	}

    public static function gp_subtitle_settings_pages() {
	?>
	    <input type="checkbox" value="1" <?php checked(get_option('subtitle-allow-on-pages', false)); ?> name="subtitle-allow-on-pages" > Yes
    <?php
	
    }

    /*
        * Used to initilise the plugin
    */

    public static function gp_init_actions(){
        add_action( 'edit_form_after_title', array( __CLASS__, 'gp_add_subtitle_with_form' ) ); 
        add_action( 'save_post', array( __CLASS__, 'gp_save_subtitle' )  );
        add_filter('the_content', array( __CLASS__, 'gp_print_subtile_after_title' )  ); 
        add_action( 'admin_init', array( __CLASS__, 'wp_register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'gp_register_assets' ) );
    }

}
add_action( 'plugins_loaded', array( 'Gp_Subtitle', 'gp_init_actions' ) );


endif;

