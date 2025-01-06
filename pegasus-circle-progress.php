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

	function pegasus_circle_progress_admin_table_css() {
		if ( circle_progress_check_main_theme_name() == 'Pegasus' || circle_progress_check_main_theme_name() == 'Pegasus Child' ) {
			//do nothing
		} else {
			//wp_register_style('circle_progress-admin-table-css', trailingslashit(plugin_dir_url(__FILE__)) . 'css/pegasus-circle_progress-admin-table.css', array(), null, 'all');
			ob_start();
			?>
				pre {
					background-color: #f9f9f9;
					border: 1px solid #aaa;
					page-break-inside: avoid;
					font-family: monospace;
					font-size: 15px;
					line-height: 1.6;
					margin-bottom: 1.6em;
					max-width: 100%;
					overflow: auto;
					padding: 1em 1.5em;
					display: block;
					word-wrap: break-word;
				}
				input[type="text"].code {
					width: 100%;
				}
				table.pegasus-table {
					width: 100%;
					border-collapse: collapse;
					border-color: #777 !important;
				}
				table.pegasus-table th {
					background-color: #f1f1f1;
					text-align: left;
				}
				table.pegasus-table th,
				table.pegasus-table td {
					border: 1px solid #ddd;
					padding: 8px;
				}
				table.pegasus-table tr:nth-child(even) {
					background-color: #f2f2f2;
				}
				table.pegasus-table thead tr { background-color: #282828; }
				table.pegasus-table thead tr td { padding: 10px; }
				table.pegasus-table thead tr td strong { color: white; }
				table.pegasus-table tbody tr:nth-child(0) { background-color: #cccccc; }
				table.pegasus-table tbody tr td { padding: 10px; }
				table.pegasus-table code { color: #d63384; }

			<?php
			// Get the buffered content
			$inline_css = ob_get_clean();

			wp_register_style('circle-progress-admin-table-css', false);
			wp_enqueue_style('circle-progress-admin-table-css');

			wp_add_inline_style('circle_progress-admin-table-css', $inline_css);
		}
	}

	add_action('admin_enqueue_scripts', 'pegasus_circle_progress_admin_table_css');

	function circle_progress_check_main_theme_name() {
		$current_theme_slug = get_option('stylesheet'); // Slug of the current theme (child theme if used)
		$parent_theme_slug = get_option('template');    // Slug of the parent theme (if a child theme is used)

		//error_log( "current theme slug: " . $current_theme_slug );
		//error_log( "parent theme slug: " . $parent_theme_slug );

		if ( $current_theme_slug == 'pegasus' ) {
			return 'Pegasus';
		} elseif ( $current_theme_slug == 'pegasus-child' ) {
			return 'Pegasus Child';
		} else {
			return 'Not Pegasus';
		}
	}

	function pegasus_circle_progress_menu_item() {
		if ( circle_progress_check_main_theme_name() == 'Pegasus' || circle_progress_check_main_theme_name() == 'Pegasus Child' ) {
			//do nothing
		} else {
			//echo 'This is NOT the Pegasus theme';
			add_menu_page(
				"Circle Progress", // Page title
				"Circle Progress", // Menu title
				"manage_options", // Capability
				"pegasus_circle_progress_plugin_options", // Menu slug
				"pegasus_circle_progress_plugin_settings_page", // Callback function
				null, // Icon
				83 // Position in menu
			);
		}
	}
	add_action("admin_menu", "pegasus_circle_progress_menu_item");

	function pegasus_circle_progress_plugin_settings_page() { ?>
	    <div class="wrap pegasus-wrap">
			<h1>Circle Progress Usage</h1>

			<div>
				<h3>Circle Progress Usage 1:</h3>
				<pre >[circle_progress number="90"]</pre>

				<input
					type="text"
					readonly
					value="<?php echo esc_html('[circle_progress number="90"]'); ?>"
					class="regular-text code"
					id="my-shortcode"
					onClick="this.select();"
				>
			</div>

			<p style="color:red;">MAKE SURE YOU DO NOT HAVE ANY RETURNS OR <?php echo htmlspecialchars('<br>'); ?>'s IN YOUR SHORTCODES, OTHERWISE IT WILL NOT WORK CORRECTLY</p>

			<div>
				<?php echo pegasus_circle_progress_settings_table(); ?>
			</div>
		</div>
	<?php
	}

	function pegasus_circle_progress_settings_table() {

		$data = json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'settings.json' ), true );

		if (json_last_error() !== JSON_ERROR_NONE) {
			return '<p style="color: red;">Error: Invalid JSON provided.</p>';
		}

		// Start building the HTML
		$html = '<table border="0" cellpadding="1" class="table pegasus-table" align="left">
		<thead>
		<tr style="background-color: #282828;">
		<td <span><strong>Name</strong></span></td>
		<td <span><strong>Attribute</strong></span></td>
		<td <span><strong>Options</strong></span></td>
		<td <span><strong>Description</strong></span></td>
		<td <span><strong>Example</strong></span></td>
		</tr>
		</thead>
		<tbody>';

		// Iterate over the data to populate rows
		if (!empty($data['rows'])) {
			foreach ($data['rows'] as $section) {
				// Add section header
				$html .= '<tr >';
				$html .= '<td colspan="5">';
				$html .= '<span>';
				$html .= '<strong>' . htmlspecialchars($section['section_name']) . '</strong>';
				$html .= '</span>';
				$html .= '</td>';
				$html .= '</tr>';

				// Add rows in the section
				foreach ($section['rows'] as $row) {
					$html .= '<tr>
						<td >' . htmlspecialchars($row['name']) . '</td>
						<td >' . htmlspecialchars($row['attribute']) . '</td>
						<td >' . nl2br(htmlspecialchars($row['options'])) . '</td>
						<td >' . nl2br(htmlspecialchars($row['description'])) . '</td>
						<td ><code>' . htmlspecialchars($row['example']) . '</code></td>
					</tr>';
				}
			}
		}

		$html .= '</tbody></table>';

		// Return the generated HTML
		return $html;
	}

	function pegasus_circle_progress_plugin_styles() {
		wp_enqueue_style( 'circle_progress-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/circle_progress.css', array(), null, 'all' );
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
		wp_register_script( 'pegasus-circle-progress-plugin-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/pegasus-circle-plugin.js', array( 'jquery' ), null, 'all' );

	} //end function
	add_action( 'wp_enqueue_scripts', 'pegasus_circle_progress_plugin_js' );



	/*~~~~~~~~~~~~~~~~~~~~
		CIRCLE PROGRESS
	~~~~~~~~~~~~~~~~~~~~~*/
	// [circle_progress number="90"]
	function pegasus_circle_prog_func( $atts, $content = null ) {
		$a = shortcode_atts( array(
			'number' => '',
			'color' => '',
		), $atts );

		if ( empty( $a['number'] ) ) {
			return '<p style="color: red;">Error: You must provide a number attribute for the circle_progress shortcode to work.</p>';
		}
		if ( empty( $a['color'] ) ) {
			$a['color'] = '#3498db';
		}

		$output = '';

		$output .= '<div class="progressbar" data-animate="false" data-color="' . $a['color'] . '">';
			$output .= "<div class='circle' data-percent='{$a['number']}'>";
				$output .= "<div>{$a['number']}%</div>";
			$output .= '</div>';
		$output .= '</div>';

		wp_enqueue_script( 'circle-progress-js' );
		wp_enqueue_script( 'pegasus-circle-progress-plugin-js' );

		return $output;
	}
	add_shortcode( 'circle_progress', 'pegasus_circle_prog_func' );
