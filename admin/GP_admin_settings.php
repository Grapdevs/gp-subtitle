<?php 
/**
* Please ignore this file. 
* This file handles tthe worpress admin settings othe than subititle
* @since 2.0.1
*/

global $pagenow;

if ( $pagenow == 'plugins.php' ) {

    add_filter( 'plugin_row_meta', 'gp_add_options_row_under_plugin_php', 10, 4 );
    function gp_add_options_row_under_plugin_php( $plugin_meta, $plugin_file, $plugin_data, $status ) {
    
        if ( strpos( $plugin_file, 'Gp_Subtitle.php' ) !== false ) {
            $new_links = array(
                'Feedback/Suggestions' => '<a href="https://docs.google.com/forms/d/e/1FAIpQLScGIAZnnfBedToN6DtIo1KsruA9nCyzcfqtWQ6nfJX9AvALiw/viewform?usp=sf_link" target="_blank">Feedback & Suggestions</a>',
                'donate' => '<a href="https://liberapay.com/grapdevs/donate" target="_blank">Donate</a>',
                );
            $plugin_meta = array_merge( $plugin_meta, $new_links );
        }
        
        return $plugin_meta;
    }
}


/**
* 
* @since 2.0.1
*/

class GPPostPage {
	private $gp_post_page_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'gp_post_page_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'gp_post_page_page_init' ) );
	}

	public function gp_post_page_add_plugin_page() {
		add_menu_page(
			'GP Post/Page', // page_title
			'GP - Post & Page', // menu_title
			'manage_options', // capability
			'gp-post-page', // menu_slug
			array( $this, 'gp_post_page_create_admin_page' ), // function
            gp_logo_subtitle, // icon_url
			10 // position
		);
	}

	public function gp_post_page_create_admin_page() {
		$this->gp_post_page_options = get_option( 'gp_post_page_option_name' ); ?>

		<div class="wrap">
			<h2>GP Post/Page</h2>
			<p>Add Subtitle to Post/Page or custom types</p>
			<?php settings_errors(); ?>
  
			<form id="gp-subtitle-setting-form" method="post" action="options.php">
				<?php
					settings_fields( 'gp_post_page_option_group' );
					do_settings_sections( 'gp-post-page-admin' );
					submit_button();
				?>
			</form>
		</div>
        <div class="wrap bg-gp-default" style="margin-top:50px;">
        <h1><u>Methods & Shortcodes</u></h1>
            <div>
                <h3>1) Method to Fetch Subtitle for API/Custom code</h3>
                <p><b>gp_get_subtitle() or gp_get_subtitle($post_id)</b> <br>: If you are working with your custom code and you would like to fetch the subtitle, you can use this method with or without parameter.
                    <Br>
                    <span style='color:red'>We will recommend you to pass $post_id(parameter), if you already have.</span>
                </p>
            </div>
            <div style="margin-top:50px;">
                <h3>2) Shortcode to fetch Subtitle</h3>
                <p><b>[gp_subtitle] or [gp_subtitle id="YOUR POST/PAGE ID"]</b> <br>
                  
                    <span style='color:red'>We will recommend you to pass your post/page id, if you already have.</span>
                </p>
            </div>
        </div>
	<?php }

	public function gp_post_page_page_init() {
		register_setting(
			'gp_post_page_option_group', // option_group
			'gp_post_page_option_name', // option_name
			array( $this, 'gp_post_page_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'gp_post_page_setting_section', // id
			'Settings For SubTitle', // title
			array( $this, 'gp_post_page_section_info' ), // callback
			'gp-post-page-admin' // page
		);

		add_settings_field(
			'subtitle-allow-on-posts', // id
			'Allow Sub-Title for Posts', // title
			array( $this, 'allow_sub_title_for_posts_0_callback' ), // callback
			'gp-post-page-admin', // page
			'gp_post_page_setting_section' // section
		);

		add_settings_field(
			'subtitle-allow-on-pages', // id
			'Allow Sub-Title for pages', // title
			array( $this, 'allow_sub_title_for_pages_1_callback' ), // callback
			'gp-post-page-admin', // page
			'gp_post_page_setting_section' // section
		);

        add_settings_field(
			'gp_allow_sub_title_for_custom_post_type', // id
			'Allow Sub-Title for Custom Post/Page', // title
			array( $this, 'gp_allow_sub_title_for_custom_post_type_callback' ), // callback
			'gp-post-page-admin', // page
			'gp_post_page_setting_section' // section
		);
	}

	public function gp_post_page_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['subtitle-allow-on-posts'] ) ) {
			$sanitary_values['subtitle-allow-on-posts'] = $input['subtitle-allow-on-posts'];
		}

		if ( isset( $input['subtitle-allow-on-pages'] ) ) {
			$sanitary_values['subtitle-allow-on-pages'] = $input['subtitle-allow-on-pages'];
		}

        if ( isset( $input['gp_allow_sub_title_for_custom_post_type'] ) ) {
			$sanitary_values['gp_allow_sub_title_for_custom_post_type'] = $input['gp_allow_sub_title_for_custom_post_type'];
		}

		return $sanitary_values;
	}

	public function gp_post_page_section_info() {
		
	}

	public function allow_sub_title_for_posts_0_callback() {
        print_r(get_post_type());
		printf(
			'<input type="checkbox" name="gp_post_page_option_name[subtitle-allow-on-posts]" id="subtitle-allow-on-posts" value="1" %s> <label for="allow_sub_title_for_posts_0"> Posts</label>',
			( isset( $this->gp_post_page_options['subtitle-allow-on-posts'] ) && $this->gp_post_page_options['subtitle-allow-on-posts'] === '1' ) ? 'checked' : ''
		);
	}

	public function allow_sub_title_for_pages_1_callback() {
		printf(
			'<input type="checkbox" name="gp_post_page_option_name[subtitle-allow-on-pages]" id="subtitle-allow-on-pages" value="1" %s> <label for="subtitle-allow-on-pages"> Pages</label>',
			( isset( $this->gp_post_page_options['subtitle-allow-on-pages'] ) && $this->gp_post_page_options['subtitle-allow-on-pages'] === '1' ) ? 'checked' : ''
		);
	}

    public function gp_allow_sub_title_for_custom_post_type_callback()
    {
       
        foreach ( get_post_types( '', 'names' ) as $post_type ) {
            if($post_type != "post" &&  $post_type != 'page')
            {
                printf(
                    '<input type="checkbox" name="gp_post_page_option_name[gp_allow_sub_title_for_custom_post_type][]" id="gp_allow_sub_title_for_custom_post_type" value="'.$post_type.'" %s> <label for="gp_allow_sub_title_for_custom_post_type"> '. ucwords(str_replace('_', ' ', $post_type)).' - ('.$post_type .')</label>',
                    ( isset( $this->gp_post_page_options['gp_allow_sub_title_for_custom_post_type']) && in_array($post_type, $this->gp_post_page_options['gp_allow_sub_title_for_custom_post_type'])) ? 'checked' : ''
                );
                echo "<br>";
            }
           
        }
       
    }

}
if ( is_admin() )
	$gp_post_page = new GPPostPage();
