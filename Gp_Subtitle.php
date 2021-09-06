<?php 
/*
/**
 * Gp Subtitle
 *
 * Plugin Name:  Gp Subtitle for Post, Pages and Custom Type
 * Description: Enables the subtitle for pages and posts, you can easily manage the subtitle for pages or post.
 * Version:     1.1.1
 * Author:      Grapdevs
 * Author URI:  https://grapdevs.com/
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


define("gp_plugin_rootdir", dirname( __FILE__ ));
define("gp_logo_subtitle", plugins_url('/assets/images/logo.svg',__FILE__ ));


require_once gp_plugin_rootdir.'/GP_public_methods.php';
require_once gp_plugin_rootdir.'/GP_plugin_validations.php';
require_once gp_plugin_rootdir.'/admin/GP_admin_settings.php'; 



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
    * 
    * @since 2.0.1
    */
    
    public  static function gp_is_subtitle_box_enabled($post_type)
    {
        if((in_array($post_type, get_option('gp_post_page_option_name')['gp_allow_sub_title_for_custom_post_type'])) || ( $post_type === 'page' && ( isset(get_option( 'gp_post_page_option_name' )['subtitle-allow-on-pages'] ) && get_option( 'gp_post_page_option_name' )['subtitle-allow-on-pages'] === '1' )) || ( $post_type === 'post' && ( isset(get_option( 'gp_post_page_option_name' )['subtitle-allow-on-posts'] ) && get_option( 'gp_post_page_option_name' )['subtitle-allow-on-posts'] === '1' )))
        {
            return true;
        }
        return false;
    }

    public static function gp_print_subtile_after_title($content){
       
        if(self::gp_is_subtitle_box_enabled(get_post_type())){
            global $post;
            $subtitle_content = self::gp_get_subtitle( $post->ID );
            $subtitle_content = '<h2 class="gp-subtitle">' . $subtitle_content . '</h2> ';
            $subtitle_content .= $content;
            return $subtitle_content;
        }
        return $content;
	} 

    
    public static function gp_create_meta_subtitle()
    {
       
        foreach((get_option('gp_post_page_option_name')['gp_allow_sub_title_for_custom_post_type']) as $post_type ) {
            add_meta_box( 'gp-subtitle-meta-id', __( 'Add Subtitle', 'grapdevs_' ), array( __CLASS__, 'gp_subtitle_meta_box_callback' ) , $post_type);
        }
        if(( isset(get_option( 'gp_post_page_option_name' )['subtitle-allow-on-pages'] ) && get_option( 'gp_post_page_option_name' )['subtitle-allow-on-pages'] === '1' )){
            add_meta_box( 'gp-subtitle-meta-id', __( 'Add Subtitle', 'grapdevs_' ), array( __CLASS__, 'gp_subtitle_meta_box_callback' ) , 'page');
        }
        if((( isset(get_option( 'gp_post_page_option_name' )['subtitle-allow-on-posts'] ) && get_option( 'gp_post_page_option_name' )['subtitle-allow-on-posts'] === '1' )))
        {
            add_meta_box( 'gp-subtitle-meta-id', __( 'Add Subtitle', 'grapdevs_' ), array( __CLASS__, 'gp_subtitle_meta_box_callback' ) , 'post');
      
        }
    }

    public static function gp_subtitle_meta_box_callback( $post ) {

        wp_nonce_field( 'global_notice_nonce', 'global_notice_nonce' );
        $value = get_post_meta( $post->ID, '_global_notice', true );

        ?>
        <div class="gp-subtitle-input" id="titlediv">
            <input type="text" id="title"  name="subtitle" value="<?php echo esc_attr( self::gp_get_subtitle( $post->ID ))?>" placeholder="Your Subtitle Here" />
        </div>
        <?php
    }

    public static function gp_sanitize( $data ){
        return isset($data) ?  filter_var($data,FILTER_SANITIZE_NUMBER_INT) : false;
    }
    
    
    public static function gp_register_assets()
    {
        wp_register_style('gp-subtitle', plugins_url('assets/gp-style.css',__FILE__ ),"", '2.0.1');
        wp_enqueue_style('gp-subtitle');
    }

    /*
        * Used to initilise the plugin
    */

    public static function gp_init_actions(){
        if (is_admin()){

            if(gp_is_classic_editor_plugin_active()){
                add_action( 'edit_form_after_title', array( __CLASS__, 'gp_add_subtitle_with_form' ) ); 
            }else{
                add_action( 'admin_init', array( __CLASS__, 'gp_create_meta_subtitle' ) ,1);
            }
            add_action( 'save_post', array( __CLASS__, 'gp_save_subtitle' )  );
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'gp_register_assets' ) );

        }else{
            add_filter( 'the_content', array( __CLASS__, 'gp_print_subtile_after_title' )  ); 
        }
    }

}
add_action( 'plugins_loaded', array( 'Gp_Subtitle', 'gp_init_actions' ) );


endif;

