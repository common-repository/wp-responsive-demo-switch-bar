<?php
/*
Plugin Name: WP Responsive Demo Switch Bar
Author: the ThemeIdol Team
Author URI: http://www.themeidol.com
Author Email: themeidol@gmail.com
Author URI: http://themeidol.com
Plugin URI: http://themeidol.com
Description: WP Responsive Demo Switch Bar Plugin - A Responsive Theme Switcher Demo Bar WordPress Plugin.
Version: 1.0
Requires at least: 4.4
Tested up to: 4.6.1
Text Domain: wpdemobar
Domain Path: /languages

This plugin inherits the GPL license from it's parent system, WordPress.
*/

/* ----------------------------------------------------------------------------------- */
/* Start Plugin Functions - Please refrain from editing this file */
/* ----------------------------------------------------------------------------------- */
/*License:

  Copyright (C) 2016 the ThemeIdol Team

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
## Allowing Direct File Access to plugin files
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'wpdemobar_class_Validator' ) ) :
class wpdemobar_class_Validator{
	/**
	 * Slug title of the setting to which this error applies
	 * as defined via the implementation of the Settings API.
	 * 
	 * @access private
	 */
	private $setting;
	
	/**
	 * Creates an instance of the class and associates the
	 * specified setting with the property of this class.
	 * 
	 * @param    string    $setting    The title of the setting we're validating
	 */
	public function __construct( $setting ) {
		$this->setting = $setting;
		//
	}
	/**
	 * Determines if the specified input is valid. For purposes of addresses,
	 * we only want to make sure the user isn't specifying an empty string.
	 * 
	 * @param    string    $input    The text string
	 * @return   bool                True if the input is valid; otherwise, false
	 */
	public function wpdemobar_is_valid( $input ) {
		
		$is_valid = true;
		
		// If the input is an empty string, add the error message and mark the validity as false
		if ( '' == trim( $input ) ) {
			
			$this->wpdemobar_add_error( 'invalid-text', __( 'One of the field is empty.','wpdemobar') );
			$is_valid = false;
		}
		
		return $is_valid;
		
	}

	
	
	/**
	 * Adds an error message to WordPress' error collection to be displayed in the dashboard.
	 * 
	 * @access   private
	 * 
	 * @param    string    $key        The key to which the specified message will be associated
	 * @param    string    $message    The message to display in the dashboard
	 */
	private function wpdemobar_add_error( $key, $message ) {
		//print_r($this->setting);
		add_settings_error(
			$this->setting,
			$key,
			$message,
			'error'
		);
		
	}
}
endif;

if ( ! class_exists( 'WP_Responsive_Demo_Switch_Bar' ) ) :
class WP_Responsive_Demo_Switch_Bar extends wpdemobar_class_Validator{
    private $assets_dir;
	private $assets_url;
	private $settings_base;
	private $settings;
	/**
	* Plugin instance.
	*
	* @var WP_Responsive_Demo_Switch_Bar The single instance of the class.
	* @since 1.0
	*/
	protected static $instance = null;


	/**
	* Main WP_Responsive_Demo_Switch_Bar Instance.
	*
	* Ensures only one instance of WP_Responsive_Demo_Switch_Bar is loaded or can be loaded.
	*
	* @since 1.0
	* @return WP_Responsive_Demo_Switch_Bar - Main instance.
	*/
	public static function instance() {
		if ( is_null( self::$instance ) ) {
				self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		define( 'WPDEMOBAR_PLUGIN_FILE', __FILE__ );
		define( 'WPDEMOBAR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'WPDEMOBAR_VERSION', $this->version );
		define( 'WPDEMOBAR_PLUGIN_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
		define( 'WPDEMOBAR_PLUGIN_URI', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
		$this->assets_dir = trailingslashit( __FILE__ ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', __FILE__ ) ) );
		$this->settings_base = 'wpt_';
		$this->icon = esc_url(  $this->assets_url.'/images/favicon.ico') ;

		// Initialise settings
		add_action( 'admin_init', array( $this, 'wpdemobar_init' ) );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'wpdemobar_register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'wpdemobar_add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ) , array( $this, 'wpdemobar_add_settings_link' ) );

		// Register post types.
        add_action( 'init', array( $this ,'wpdemobar_register_post_types' ), 25 );
        //Action to manage new metabox for site registration
        add_action( 'add_meta_boxes', array( $this, 'wpdemobar_add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'wpdemobar_save_meta_boxes' ), 10, 3 );

        // Add Admin column.
        add_filter( 'manage_demosites_posts_columns', array( $this, 'wpdemobar_custom_column_head' ) );
        add_action( 'manage_demosites_posts_custom_column', array( $this, 'wpdemobar_custom_column_content' ), 10, 2 );

        // Hide publishing actions.
        add_action( 'admin_head-post.php', array( $this, 'wpdemobar_hide_publishing_actions' ) );
        add_action( 'admin_head-post-new.php', array( $this, 'wpdemobar_hide_publishing_actions' ) );

        // Customize Row actions.
        add_filter( 'post_row_actions', array( $this, 'wpdemobar_customize_row_actions' ), 10, 2 );

        // Customize post updated messages.
        add_filter( 'post_updated_messages', array( $this, 'wpdemobar_updated_messages' ) );

		if (!is_admin()) {
			add_filter( 'template_include', array($this, 'wpdemobar_custom_template' ), 99 );
	    } 
	    // Admin Scripts
	    add_action( 'admin_enqueue_scripts', array($this, 'wpdemobar_admin_assets'));
	    //Language Support For WP Responsive Demo Switch Bar
		add_action( 'plugins_loaded', array($this,'wpdemobar_load_textdomain') );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function wpdemobar_init() {
		$this->settings = $this->wpdemobar_settings_fields();
	}

	/**
	* Load plugin textdomain.
	*
	* @since 1.2
	*/
	function wpdemobar_load_textdomain() {
		load_plugin_textdomain( 'wpdemobar', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' ); 
	}
	

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function wpdemobar_add_menu_item() {
		//$page=add_menu_page(__( 'Demo Bar', 'wpdemobar' ), __( 'Demo Bar', 'wpdemobar' ), 'manage_options', 'wpdemobar_settings', array( $this, 'settings_page' ), $this->icon);
		$page = add_options_page( __( 'Demobar Settings', 'wpdemobar' ) , __( 'Demobar Settings', 'wpdemobar' ) , 'manage_options' , 'wpdemobar_settings' ,  array( $this, 'wpdemobar_settings_page' ), $this->icon );

		add_action( 'admin_print_styles-' . $page, array( $this, 'wpdemobar_settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function wpdemobar_settings_assets() {

		
        wp_enqueue_style( 'wpt-options-style', $this->assets_url . 'css/option.css', array( ), '1.0.0' );


	    // We're including the WP media scripts here because they're needed for the image upload field
	    // If you're not including an image upload then you can leave this function call out
	    wp_enqueue_media();
	    // We're including the farbtastic script & styles here because they're needed for the colour picker
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_script( 'wp-color-picker');
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic' );

	    wp_register_script( 'wpt-admin-js', $this->assets_url . 'js/settings.js', array( 'jquery', 'farbtastic','wp-color-picker' ), '1.0.0' );
	    wp_enqueue_script( 'wpt-admin-js' );
	}

	/**
	 * Load settings JS & CSS for Metabox
	 * @return void
	 */
	public function wpdemobar_admin_assets() {
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_script( 'wp-color-picker');

	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function wpdemobar_add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=wpdemobar_settings">' . __( 'Settings', 'wpdemobar' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function wpdemobar_settings_fields() {

		$settings['standard'] = array(
			'title'					=> __( 'General Settings', 'wpdemobar' ),
			'description'			=> __( 'WP Responsive Demo Switch Bar General Settings', 'wpdemobar' ),
			'fields'				=> array(
			 	array(
					'id' 			=> 'an_image',
					'label'			=> __( 'Logo' , 'wpdemobar' ),
					'description'	=> __( 'This image is used as the logo for the demo bar', 'wpdemobar' ),
					'type'			=> 'image',
					'default'		=> ''
				),
				array(
					'id' 			=> 'demo_theme',
					'label'			=> __( 'Select Demo Bar Theme', 'wpdemobar' ),
					'description'	=> __( 'This would switch your demo bar to light and dark color bar.(Available only in Premium Plugin.Premium plugin at <a href="http://themeidol.com">Themeidol</a>)', 'wpdemobar' ),
					'type'			=> 'select',
					'options'		=>array( 'dark' => 'Dark', 'light' => 'Light'),
					'default'		=> array('dark')
				),
				array(
					'id' 			=> 'demo_updown',
					'label'			=> __( 'Select Demo Bar Position', 'wpdemobar' ),
					'description'	=> __( 'This would switch your demo bar to top or bottom position. By default its Bottom position(Available only in Premium Plugin.Premium plugin at <a href="http://themeidol.com">Themeidol</a>)', 'wpdemobar' ),
					'type'			=> 'select',
					'options'		=>array( 'top' => 'Top', 'bottom' => 'Bottom'),
					'default'		=> array('bottom')
				),
				
				array(
					'id' 			=> 'responsive',
					'label'			=> __( 'Responsive button', 'wpdemobar' ),
					'description'	=> __( 'This feature is to to show hide the responsive display for various devices', 'wpdemobar' ),
					'type'			=> 'checkbox_multi',
					'options'		=> array( '1' => 'Yes', '0' => 'No'),
					'default'		=> array( 'no' )
				),
				array(
					'id' 			=> 'close',
					'label'			=> __( 'Close button', 'wpdemobar' ),
					'description'	=> __( 'This would make the close button so close the demo of any theme or url on site', 'wpdemobar' ),
					'type'			=> 'checkbox_multi',
					'options'		=> array( '1' => 'Yes', '0' => 'No'),
					'default'		=> array( 'no' )
				),
				array(
					'id' 			=> 'purchase',
					'label'			=> __( 'Purchase/Download button', 'wpdemobar' ),
					'description'	=> __( 'This would make hide/show the downlaod/purchase buttona according to free or premium template ', 'wpdemobar' ),
					'type'			=> 'checkbox_multi',
					'options'		=> array( '1' => 'Yes', '0' => 'No'),
					'default'		=> array( 'no' )
				),
				array(
					'id' 			=> 'share',
					'label'			=> __( 'Share button', 'wpdemobar' ),
					'description'	=> __( 'Show/Hide Social Share Button (Only 3 social icons Facebook, Tumblr and WordPress is avaialble .For more social icons please visit <a href="http://themeidol.com">Themeidol</a>)', 'wpdemobar' ),
					'type'			=> 'checkbox_multi',
					'options'		=> array( '1' => 'Yes', '0' => 'No'),
					'default'		=> array( 'no' )
				),


			)
		);

		$settings['extra'] = array(
			'title'					=> __( 'Page Settings', 'wpdemobar' ),
			'description'			=> __( 'WP Responsive Demo Switch Bar Page Settings', 'wpdemobar' ),
			'fields'				=> array(
				array(
					'id' 			=> 'page_icon',
					'label'			=> __( 'Page Icon' , 'wpdemobar' ),
					'description'	=> __( 'This will upload an image to your media library and store the attachment ID in the option field. Once you have uploaded an imge the thumbnail will display above these buttons.', 'wpdemobar' ),
					'type'			=> 'image',
					'default'		=> '',
					
				),
				array(
					'id' 			=> 'page_title',
					'label'			=> __( 'Page Title' , 'wpdemobar' ),
					'description'	=> __( 'WP Responsive Demo Switch Bar Page title', 'wpdemobar' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'Page Title', 'wpdemobar' ),
					'callback'		=> array($this,'wpdemobar_sanitize_text')
				),
				array(
					'id' 			=> 'meta_description',
					'label'			=> __( 'WP Responsive Demo Switch Bar Demo page for Meta Description for SEO' , 'wpdemobar' ),
					'description'	=> __( 'This is a standard text area.', 'wpdemobar' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text for this textarea', 'wpdemobar' ),
					'callback'		=> array($this,'wpdemobar_sanitize_text')

				),
				array(
					'id' 			=> 'meta_keywords',
					'label'			=> __( 'Meta Keywords' , 'wpdemobar' ),
					'description'	=> __( 'WP Responsive Demo Switch Bar Demo page for Meta Keyword for SEO', 'wpdemobar' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text for this textarea', 'wpdemobar' ),
					'callback'		=> array($this,'wpdemobar_sanitize_text')
				),

				array(
					'id' 			=> 'page_slug',
					'label'			=> __( 'WP Responsive Demo Switch Bar for Page slug', 'wpdemobar' ),
					'description'	=> __( 'A standard select box.', 'wpdemobar' ),
					'type'			=> 'select',
					'options'		=> $this->wpdemobar_get_pages(),
					'default'		=> ''
				)
				
			)
		);

		$settings = apply_filters( 'wpdemobar_plugin_settings_fields', $settings );

		return $settings;
	}
	/**
	 * Get lists of Pages in WP
	 * @return array Fields to be displayed on settings page
	 */
	function wpdemobar_get_pages($args='')
	{
		        $defaults = array(
	                'depth' => 0, 
	                'child_of' => 0,
	                'selected' => 0,
	                'name' => 'page_id',
	                'id' => '',
	                'value_field' => 'ID',
	        );
		    $data_array=array();
	
	        $r = wp_parse_args( $args, $defaults );
	
	        $pages = get_pages( $r );
	         foreach ( $pages as $page ) {
	         	$data_array[$page->ID]=$page->post_title;
	         }
	       
	       
	        return $data_array;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function wpdemobar_register_settings() {
		if( is_array( $this->settings ) ) {
			foreach( $this->settings as $section => $data ) {

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'wpdemobar_settings_section' ), 'plugin_settings' );

				foreach( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->settings_base . $field['id'];
					register_setting( 'plugin_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this, 'wpdemobar_display_field' ), 'plugin_settings', $section, array( 'field' => $field ) );
				}
			}
		}
	}

	public function wpdemobar_settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Generate HTML for displaying fields
	 * @param  array $args Field data
	 * @return void
	 */
	public function wpdemobar_display_field( $args ) {

		$field = $args['field'];

		$html = '';

		$option_name = $this->settings_base . $field['id'];
		$option = get_option( $option_name );

		$data = '';
		if( isset( $field['default'] ) ) {
			$data = $field['default'];
			if( $option ) {
				$data = $option;
			}
		}

		switch( $field['type'] ) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
			break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value=""/>' . "\n";
			break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;

			case 'checkbox':
				$checked = '';
				if( $option && 'on' == $option ){
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;

			case 'checkbox_multi':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" class="chb" /> ' . $v . '</label> ';
				}
			break;

			case 'radio':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
			break;

			case 'image':
				$image_thumb = '';
				if( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image' , 'wpdemobar' ) . '" data-uploader_button_text="' . __( 'Use image' , 'wpdemobar' ) . '" class="image_upload_button button" value="'. __( 'Upload new image' , 'wpdemobar' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. __( 'Remove image' , 'wpdemobar' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
			break;

			case 'color':
				?><div class="color-picker" style="position:relative;">
			        <input type="text" name="<?php esc_attr_e( $option_name ); ?>" class="color" value="<?php esc_attr_e( $data ); ?>" />
			        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
			    </div>
			    <?php
			break;

		}

		switch( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;

			default:
				$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
			break;
		}

		echo $html;
	}

	/**
 	 * A custom sanitization function that will take the incoming input, and sanitize
 	 * the input before handing it back to WordPress to save to the database. 
 	 * If the input is not valid, then the data will not be saved and an error 
 	 * message will be added to WordPress' error collection.
  	 *
  	 * @since    1.0.0
  	 *
  	 * @param    array    $input        Text input.
  	 * @return   mixed    $new_input    The sanitized input or false (to prevent the data from saving)
  	 */
 	public function wpdemobar_sanitize_text( $input ) {

		$new_input = false;

		if ( parent::wpdemobar_is_valid( $input ) ) {
			
			$new_input = sanitize_text_field( $input );
			
		}
		
		return $new_input;
	
 	}
 

	/**
	 * Load settings page content
	 * @return void
	 */
	public function wpdemobar_settings_page() {

		// Build page HTML
		
		$html = '<div id="plugin_settings">' . "\n";
			$html .= ' <div class="icon32" id="icon-tools"> <br /> </div> <h1>' . __( 'WP Responsive Demo Switch Bar Settings' , 'wpdemobar' ) . '</h1>' . "\n";
			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Setup navigation
				$html .= '<ul id="settings-sections" class="tabrow hide-if-no-js">' . "\n";
					$i=0;
					foreach( $this->settings as $section => $data ) {
						$current=($i==0)?'current':'';
						$html .= '<li class="'.$current.'"><a class="tab ' . $section . ' ' . $current . '" href="#' . $section . '">' . $data['title'] . '</a></li>' . "\n";
						$i++;
					}

				$html .= '</ul>' . "\n";

				$html .= '<div class="clear"></div>' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( 'plugin_settings' );
				do_settings_sections( 'plugin_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'wpdemobar' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}

	/**
     * Register core post types.
     */
    public  function wpdemobar_register_post_types() {
        if ( post_type_exists( 'demosites' ) ) {
            return;
        }
        $labels = array(
            'name'                  => __( 'Demo Site', 'wpdemobar' ),
            'singular_name'         => __( 'Site', 'wpdemobar' ),
            'menu_name'             => __( 'Demo Site', 'wpdemobar' ),
            'all_items'             => __( 'Sites', 'wpdemobar' ),
            'add_new'               => __( 'Add Site', 'wpdemobar' ),
            'add_new_item'          => __( 'Add New Site', 'wpdemobar' ),
            'edit'                  => __( 'Edit', 'wpdemobar' ),
            'edit_item'             => __( 'Edit Site', 'wpdemobar' ),
            'new_item'              => __( 'New Site', 'wpdemobar' ),
            'view'                  => __( 'View Site', 'wpdemobar' ),
            'view_item'             => __( 'View Site', 'wpdemobar' ),
            'search_items'          => __( 'Search Sites', 'wpdemobar' ),
            'not_found'             => __( 'No Sites found', 'wpdemobar' ),
            'not_found_in_trash'    => __( 'No Sites found in trash', 'wpdemobar' ),
            'parent'                => __( 'Parent Site', 'wpdemobar' ),
            'featured_image'        => __( 'Site Image', 'wpdemobar' ),
            'set_featured_image'    => __( 'Set site image', 'wpdemobar' ),
            'remove_featured_image' => __( 'Remove site image', 'wpdemobar' ),
            'use_featured_image'    => __( 'Use as site image', 'wpdemobar' ),
        );
        $args = array(
            'public'             => true,
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => false,
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_icon'          => 'dashicons-admin-site',
            'supports'           => array( 'title','thumbnail'),
        );
        register_post_type( 'demosites', $args );

        // Flush rules after registration of post type.
		flush_rewrite_rules();
    }
    /**
    * Add Post meta box
    *
    */

        public function wpdemobar_add_meta_boxes() {
        add_meta_box(
            'demosite-settings',
            esc_html__( 'Site Info', 'wpdemobar' ),
            array( $this, 'wpdemobar_render_site_settings_metabox' ),
            'demosites',
            'normal',
            'high'
        );
    }
    /**
     * Render site settings metabox.
     *
     * @since 1.0.0
     *
     * @param WP_Post $post    WP_Post object.
     * @param array   $metabox Metabox arguments.
     */
    function wpdemobar_render_site_settings_metabox( $post, $metabox ) {
        // Meta box nonce for verification.
        wp_nonce_field( 'demobar_save_data', 'demobar_meta_nonce' );

        $demo_bar_site_url     = get_post_meta( $post->ID, 'demo_bar_site_url', true );
        $demo_bar_download_url = get_post_meta( $post->ID, 'demo_bar_download_url', true );
        $demo_bar_type = get_post_meta( $post->ID, 'demo_bar_type', true );
        $demo_bar_type_color = get_post_meta( $post->ID, 'demo_bar_type_color', true );
        $product_type	= get_post_meta( $post->ID, 'product_type', true );
        $product_price	= get_post_meta( $post->ID, 'product_price', true );
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($){
        jQuery( '#color1' ).each( function() {
            $( this ).wpColorPicker();
        });
        });

        </script>
        <p>
            <label for="demo_bar_site_url"><?php echo esc_html__( 'Site URL', 'wpdemobar' ); ?><br /><input type="text" value="<?php echo esc_url( $demo_bar_site_url ); ?>" class="regular-text" name="demo_bar_site_url" id="demo_bar_site_url" /></label>
        </p>
        <p>
            <label for="demo_bar_download_url"><?php echo esc_html__( 'Download URL', 'wpdemobar' ); ?><br /><input type="text" value="<?php echo esc_url( $demo_bar_download_url ); ?>" class="regular-text" name="demo_bar_download_url" id="demo_bar_download_url" /></label>
        </p>
        <p>
            <label for="demo_bar_type"><?php echo esc_html__( 'Source', 'wpdemobar' ); ?><br /><input type="text" value="<?php echo esc_attr( $demo_bar_type ); ?>" class="regular-text" name="demo_bar_type" id="demo_bar_type" /></label>
        </p>
        <p>
             <label for="demo_bar_type_color"><?php _e('Source color','wpdemobar' ); ?>:</label>
             <div style="position: relative;" id="talaothat">
             <input id="color1" value="<?php echo esc_attr( $demo_bar_type_color ); ?>" class="regular-text required" name="demo_bar_type_color" type="text"/>
             </div>
            <br clear="both" />
        </p>
        <p>
            <label for="product_type"><?php echo esc_html__( 'Product Type', 'wpdemobar' ); ?>
            <br />
            <select name="product_type" id="product_type">
            	<option value="free" <?php echo (esc_attr( $product_type )=='free')?'selected="selected"':'';?>>Free</option>
            	<option value="premium" <?php echo (esc_attr( $product_type )=='premium')?'selected="selected"':'';?>>Premium</option>
            </select>
            </label>
        </p>
        <p id="metaproduct_price" <?php echo (esc_attr( $product_type )=='premium')?'class="display-price"':'';?> >
       	<label for="demo_bar_type"><?php echo esc_html__( 'Product Price ($)', 'wpdemobar' ); ?>
        <br />
        <input id="product_price" value="<?php echo esc_attr( $product_price ); ?>" class="regular-text required" name="product_price" type="text"/>

        </p>
        <?php
    }
    /**
     * Save site settings meta box.
     *
     * @since 1.0.0
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @param bool    $update  Whether this is an existing post being updated or not.
     */
    function wpdemobar_save_meta_boxes( $post_id, $post, $update ) {
        // Verify nonce.
        if ( ! isset( $_POST['demobar_meta_nonce'] ) || ! wp_verify_nonce( $_POST['demobar_meta_nonce'], 'demobar_save_data' ) ) {
            return;
        }

        // Bail if auto save or revision.
        if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
            return;
        }

        // Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
        if ( '' == $_POST['post_ID'] || absint( $_POST['post_ID'] ) !== $post_id ) {
            return;
        }

        // Check permission.
        if ( 'page' === $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
        } else if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        $site_settings_fields = array(
            'demo_bar_site_url',
            'demo_bar_download_url',
            'demo_bar_type',
            'demo_bar_type_color',
            'product_type',
            'product_price'
        );
        foreach ( $site_settings_fields as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                $post_value = $_POST[ $key ];
                if ( empty( $post_value ) ) {
                    delete_post_meta( $post_id, $key );
                } else {
                    update_post_meta( $post_id, $key, esc_attr( $post_value ) );
                }
            }
        } // End foreach loop.
    }

    /**
         * Hide publishing actions.
         *
         * @since 1.0.0
         */
        function wpdemobar_hide_publishing_actions() {
            global $post;
            if ( 'demosites' !== $post->post_type ) {
                return;
            }
            ?>
            <style type="text/css">
            #misc-publishing-actions,#minor-publishing-actions{
                display:none;
            }
            </style>
            <?php
            return;
        }

        /**
         * Customize column names.
         *
         * @since 1.0.0
         *
         * @param array $columns An array of column names.
         */
        function wpdemobar_custom_column_head( $columns ) {
            $new_columns['cb']           = '<input type="checkbox" />';
            $new_columns['title']        = $columns['title'];
            $new_columns['site_url']     = _x( 'Site URL', 'column name', 'wpdemobar' );
            $new_columns['download_url'] = _x( 'Download URL',  'column name', 'wpdemobar' );
            $new_columns['screen']         = _x( 'Preview',  'column name', 'wpdemobar' );
            return $new_columns;
        }
        /**
         * Customize column content.
         *
         * @since 1.0.0
         *
         * @param string $column_name The name of the column to display.
         * @param int    $post_id     The current post ID.
         */
        function wpdemobar_custom_column_content( $column_name, $post_id ) {
            switch ( $column_name ) {
                case 'site_url':
                    echo esc_url( get_post_meta( $post_id, 'demo_bar_site_url', true ) );
                    break;
                case 'download_url':
                    echo esc_url( get_post_meta( $post_id, 'demo_bar_download_url', true ) );
                    break;
                case 'screen':
                    echo get_the_post_thumbnail( $post_id, 'thumbnail' );;
                    break;
                default:
                    break;
            }
        }

        /**
         * Customize row actions.
         *
         * @since 1.0.0
         *
         * @param array   $actions An array of row action links.
         * @param WP_Post $post    The post object.
         */
        function wpdemobar_customize_row_actions( $actions, $post ) {
            if ( 'demosites' === $post->post_type ) {
                unset( $actions['inline hide-if-no-js'] );
            }
            return $actions;
        }

        /**
         * Customize post updated messages.
         *
         * @since 1.0.0
         *
         * @param array $messages Existing post update messages.
         * @return array Amended post update messages with new CPT update messages.
         */
        function wpdemobar_updated_messages( $messages ) {

            $_post            = get_post();
            $post_type        = get_post_type( $_post );
            $post_type_object = get_post_type_object( $post_type );

            $messages['demosites'] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => __( 'Site updated.', 'wpdemobar' ),
            2  => __( 'Custom field updated.', 'wpdemobar' ),
            3  => __( 'Custom field deleted.', 'wpdemobar' ),
            4  => __( 'Site updated.', 'wpdemobar' ),
            /* translators: %s: date and time of the revision */
            5  => isset( $_GET['revision'] ) ? sprintf( __( 'Site restored to revision from %s', 'wpdemobar' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => __( 'Site published.', 'wpdemobar' ),
            7  => __( 'Site saved.', 'wpdemobar' ),
            8  => __( 'Site submitted.', 'wpdemobar' ),
            9  => sprintf(
                __( 'Site scheduled for: <strong>%1$s</strong>.', 'wpdemobar' ),
                date_i18n( __( 'M j, Y @ G:i', 'wpdemobar' ), strtotime( $_post->post_date ) )
            ),
            10 => __( 'Site draft updated.', 'wpdemobar' ),
            );

            return $messages;
        }




    /**
	 * Load custom template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Template.
	 */
	public static function wpdemobar_custom_template( $template ) {
		$page_slug = get_option( 'wpt_page_slug' );
		if ( isset( $page_slug ) && absint( $page_slug ) > 0 ) {
			if ( is_page( absint( $page_slug ) ) ) {
				$custom_template = plugin_dir_path( WPDEMOBAR_PLUGIN_FILE ) . 'views/layout/themesdemo.php';
				if ( $custom_template ) {
					return $custom_template;
				}
			}
		}
		return $template;
	}



	/**
	 * Custom title.
	 *
	 * @since 1.0.0
	 */
	public static function wpdemobar_custom_title() {
		?>
		
			<title><?php echo esc_attr(get_option('wpt_page_title')); ?></title>
		
		<?php
	}

	/**
	 * Fetch site list.
	 *
	 * @since 1.0.0
	 */
	public static function wpdemobar_get_sites() {
		$output = array();
		$qargs = array(
			'post_type'      => 'demosites',
			'no_found_rows'  => true,
			'posts_per_page' => -1,
		);
		$all_posts = get_posts( $qargs );
		if ( $all_posts ) {
			foreach ( $all_posts as $p ) {
				$item = array();
				$item['ID']           	= $p->ID;
				$item['title']        	= apply_filters( 'the_title', $p->post_title );
				$item['slug']         	= $p->post_name;
				$item['site_url']     	= get_post_meta( $p->ID, 'demo_bar_site_url', true );
				$item['download_url'] 	= get_post_meta( $p->ID, 'demo_bar_download_url', true );
				$item['type']		  	= get_post_meta($p->ID,'demo_bar_type',true);
				$item['color']			= get_post_meta($p->ID,'demo_bar_type_color',true);
				$item['screen'] 		= wp_get_attachment_image_src( get_post_thumbnail_id( $p->ID ), 'thumbnail' );
				$item['ptype']			= get_post_meta($p->ID,'product_type',true);
				$item['price']			= get_post_meta($p->ID,'product_price',true);
				$output[] 				= $item;
			}
		}
		return $output;
	}
	function wpdemobar_is_firefox() {
	    $agent = '';
	    // old php user agent can be found here
	    if (!empty($HTTP_USER_AGENT))
	        $agent = $HTTP_USER_AGENT;
	    // newer versions of php do have useragent here.
	    if (empty($agent) && !empty($_SERVER["HTTP_USER_AGENT"]))
	        $agent = $_SERVER["HTTP_USER_AGENT"];
	    if (!empty($agent) && preg_match("/firefox/si", $agent))
	        return true;
	    return false;
	}

	function wpdemobar_is_windows() {
	    $agent = '';
	    // old php user agent can be found here
	    if (!empty($HTTP_USER_AGENT))
	        $agent = $HTTP_USER_AGENT;
	    // newer versions of php do have useragent here.
	    if (empty($agent) && !empty($_SERVER["HTTP_USER_AGENT"]))
	        $agent = $_SERVER["HTTP_USER_AGENT"];
	    if (!empty($agent) && preg_match("/windows/si", $agent))
	        return true;
	    return false;
		}
   
	}
endif;

	/**
	 * Main instance of WP Responsive Demo Bar.
	 *
	 * Returns the main instance of WP_Responsive_Demo_Switch_Bar to prevent the need to use globals.
	 *
	 * @since  1.0.
	 * @return settings
	 */
	function wpdemobar_settings() {
		return WP_Responsive_Demo_Switch_Bar::instance();
	}

	// Global for backwards compatibility.
	$GLOBALS['wpdemobar_settings'] = wpdemobar_settings();


