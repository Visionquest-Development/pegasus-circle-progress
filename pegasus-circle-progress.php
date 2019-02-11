<?php
/*
Plugin Name: Pegasus Circle Progress Plugin
Plugin URI:  https://developer.wordpress.org/plugins/the-basics/
Description: This allows you to create circles that count up, starting at 0, and end at a set number on your website with just a shortcode.
Version:     1.0
Author:      Jim O'Brien
Author URI:  https://visionquestdevelopment.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wporg
Domain Path: /languages
*/
	/**
	 * Silence is golden; exit if accessed directly
	 */
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	function pegasus_circle_progress_menu_item() {
		//add_menu_page("Circle Progress", "Circle Progress", "manage_options", "pegasus_circle_progress_plugin_options", "pegasus_circle_progress_plugin_settings_page", null, 99);
		
	}
	add_action("admin_menu", "pegasus_circle_progress_menu_item");
	function pegasus_circle_progress_plugin_settings_page() { ?>
	    <div class="wrap pegasus-wrap">
	    <h1>Circle Progress</h1>			
			<p>Shortcode Usage: <pre>[circle_progress number="90"]  </pre></p>	
			
		</div>
	<?php
	}
	
	function pegasus_circle_progress_plugin_styles() {
		//wp_enqueue_style( 'circle_progress-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/circle_progress.css', array(), null, 'all' );
		//wp_enqueue_style( 'slippery-slider-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/slippery-slider.css', array(), null, 'all' );
	}
	add_action( 'wp_enqueue_scripts', 'pegasus_circle_progress_plugin_styles' );
	
	/**
	* Proper way to enqueue JS 
	*/
	function pegasus_circle_progress_plugin_js() {
		
		
		//wp_enqueue_script( 'waypoints-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/waypoints.js', array( 'jquery' ), null, true );
		
		//wp_enqueue_script( 'images-loaded-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/imagesLoaded.js', array( 'jquery' ), null, true );

		wp_register_script( 'circle-progress-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/circle-progress.js', array( 'jquery' ), null, 'all' );
		wp_register_script( 'pegasus-circle-progress-plugin-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/plugin.js', array( 'jquery' ), null, 'all' );
		
	} //end function
	add_action( 'wp_enqueue_scripts', 'pegasus_circle_progress_plugin_js' );
	
		
		
	/*~~~~~~~~~~~~~~~~~~~~
		CIRCLE PROGRESS
	~~~~~~~~~~~~~~~~~~~~~*/
	// [circle_progress number="90"] 
	function pegasus_circle_prog_func( $atts, $content = null ) {
		$a = shortcode_atts( array(
			'number' => '',
		), $atts );
	
		$output = '<div class="progressbar" data-animate="false">';
			$output .= "<div class='circle' data-percent='{$a['number']}'>";
				$output .= "<div>{$a['number']}%</div>";
			$output .= '</div>';
		$output .= '</div>';

		wp_enqueue_script( 'circle-progress-js' );
		wp_enqueue_script( 'pegasus-circle-progress-plugin-js' );

		return $output; 
	}
	add_shortcode( 'circle_progress', 'pegasus_circle_prog_func' );
	