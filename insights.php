<?php
/*
 * Plugin Name: Insights
 * Plugin URI: https://github.com/trepmal/Insights/
 * Description: Insights allows you to quickly search and insert information (links, images, videos, maps, news..) into your blog posts.
 * Version: 2
 * Author: Vladimir Prelovac / Kailey Lampert
 * Author URI: kaileylampert.com
 * License: GPLv2 or later
 * TextDomain:
 * DomainPath:
 * Network:
 */

// Avoid name collisions.
if ( !class_exists('WPInsights') ) :

class WPInsights {

	// Name for our options in the DB
	var $DB_option = 'WPInsights_options';
	// var $plugin_url;

	// Initialize WordPress hooks
	function WPInsights() {
		// $this->plugin_url = defined('WP_PLUGIN_URL') ? WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)) : trailingslashit(get_bloginfo('wpurl')) . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));

		// Add Options Page
		add_action('admin_menu',  array(&$this, 'admin_menu'));

		add_action('wp_ajax_insights',  array(&$this, 'insights_cb'));

		// print scripts action
		add_action('admin_print_scripts-post.php',  array(&$this, 'scripts_action'));
		add_action('admin_print_scripts-page.php',  array(&$this, 'scripts_action'));
		add_action('admin_print_scripts-post-new.php',  array(&$this, 'scripts_action'));
		add_action('admin_print_scripts-page-new.php',  array(&$this, 'scripts_action'));

		//add_action( 'init', array( &$this, 'add_tinymce' ));

	}

	function insights_cb() {
		// echo '**';
		include( 'insights-ajax.php' );
	}

	function add_tinymce() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) )
			return;

		if ( get_user_option('rich_editing') == 'true' ) {

			add_filter( 'mce_external_plugins', array( &$this, 'add_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( &$this, 'add_tinymce_button' ));
		}
	}

	function add_tinymce_button( $buttons ) {
		array_push( $buttons, "separator", 'btnInsights' );
		return $buttons;
	}

	function add_tinymce_plugin( $plugin_array ) {
		$plugin_array['insights'] = plugins_url( '/insights-mceplugin.js', __FILE__ );
		return $plugin_array;
	}

	function scripts_action() {
			$options=$this->get_options();

			$interactive = $options['interactive'] ? 1 : 0; ;

			$nonce = wp_create_nonce('insights-nonce');

			wp_enqueue_script('jquery');
			wp_enqueue_script('jQuery.jCache', plugins_url( '/js/jQuery.jCache.js', __FILE__), array('jquery') );

			global $content_width;
			if ( is_null( $content_width ) ) $content_width = 600;
			wp_enqueue_script('insights', plugins_url( '/js/insights.js', __FILE__ ), array('jquery', 'jQuery.jCache'), 1, true);
			wp_localize_script('insights', 'InsightsSettings', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'content_width' => $content_width,
				'insights_url' => plugins_url('', __FILE__),
				'insights_interactive' => $interactive,
				'nonce' => $nonce
			) );

			if ( ! empty( $options['maps_api'] ) ) {
				wp_enqueue_script('googlemapsapi', 'http://maps.googleapis.com/maps/api/js?sensor=false', array(), 3, true );
				wp_enqueue_script('insightsmaps', plugins_url( '/js/insights-maps.js', __FILE__ ), array( 'insights', 'googlemapsapi'), 1, true);
				wp_localize_script('insights', 'InsightsMapSettings', array(
					'content_width' => $content_width,
					'v3api' => $options['maps_api'],
				) );
			}

	}


	function draw_insights() {
		$options = $this->get_options();
	?>
<p>Enter keywords you would like to search for and press Search button.</p>
<input type="text" id="insights-search" name="insights-search" size="60" autocomplete="off"/>
<input id="insights-submit" class="button" type="button" value="Search"  /> <br />

<label><input name="insights-radio" type="radio" checked="" value="1" /> My Blog</label>
<label><input name="insights-radio" type="radio" value="2"/> Images</label>
<label><input name="insights-radio" type="radio" value="3"/> Videos</label>
<label><input name="insights-radio" type="radio" value="4"/> Wikipedia</label>
<label><input name="insights-radio" type="radio" value="6"/> Google</label>
<label><input name="insights-radio" type="radio" value="7"/> News</label>
<label><input name="insights-radio" type="radio" value="10"/> Blogs</label>
<label><input name="insights-radio" type="radio" value="11"/> Books</label>
<?php if ( ! empty( $options['maps_api'] ) ) : ?>
<label><input name="insights-radio" type="radio" value="5"/> Maps</label>
<?php endif; ?>

<div id="insights-results"></div>
	<div id="insights-map-all" style="display:none" >
		<p>
		<input class="button" type="button" value="Add Map" onclick="insert_map();">
		<input class="button" type="button" value="Add Marker" onclick="createMarkerAt();">
		<input class="button" type="button" value="Clear Markers" onclick="clearMarkers();">
		<input class="button" type="button" value="Clear Path" onclick="clearPolys();">
		</p>
	<div id="insights-map" style="height:450px; width:100%; padding:0px; margin:0px;"></div>
</div>
<?php

	}

	// Hook the options mage
	function admin_menu() {

		add_options_page('Insights Options', 'Insights', 'edit_pages', basename(__FILE__), array(&$this, 'handle_options'));
		$show_on = apply_filters( 'insights_meta_box', array( 'post', 'page' ) );
		foreach( $show_on as $s ) {
			add_meta_box( 'WPInsights', 'Insights', array(&$this,'draw_insights'), $s, 'normal', 'high' );
		}
	}

	// Handle our options
	function get_options() {
		$options = array(
			'post_results' => 5,
			'image_results' => 16,
			'wiki_results' => 10,
			'video_results' => 20,
			'image_tags' => "on",
			'image_text' => "on",
			'image_nonc' => "",
			'interactive' => '',
			// 'gmaps' => '',
			'maps_api' => 'enter your key'
		);

		$saved = get_option($this->DB_option);

		if (!empty($saved)) {
			foreach ($saved as $key => $option)
				$options[$key] = $option;
		}

		if ($saved != $options)
			update_option($this->DB_option, $options);

		return $options;
	}

	// Set up everything
	function install() {
		$this->get_options();

	}

	function handle_options() {
		$options = $this->get_options();

		if ( isset($_POST['submitted']) ) {

			check_admin_referer('insights');

			$options = array();

			$options['post_results']  = (int) $_POST['post_results'];
			$options['image_results'] = (int) $_POST['image_results'];
			$options['wiki_results']  = (int) $_POST['wiki_results'];
			$options['video_results'] = (int) $_POST['video_results'];
			$options['image_tags']    = isset( $_POST['image_tags'] ) ? 'on' : '';
			$options['image_nonc']    = isset( $_POST['image_nonc'] ) ? 'on' : '';
			$options['image_text']    = isset( $_POST['image_text'] ) ? 'on' : '';
			$options['interactive']   = isset( $_POST['interactive'] ) ? 'on' : '';
			// $options['gmaps']         = isset( $_POST['gmaps'] ) ? 'on' : '';
			$options['maps_api']      = $_POST['maps_api'];

			update_option($this->DB_option, $options);
			echo '<div class="updated fade"><p>Plugin settings saved.</p></div>';
		}

		$post_results  = $options['post_results'];
		$image_results = $options['image_results'];
		$wiki_results  = $options['wiki_results'];
		$video_results = $options['video_results'];
		$image_tags    = $options['image_tags'] == 'on' ? 'checked' : '';
		$image_text    = $options['image_text'] == 'on' ? 'checked' : '';
		$image_nonc    = $options['image_nonc'] == 'on' ? 'checked' : '';
		$interactive   = $options['interactive'] == 'on' ? 'checked' : '';
		// $gmaps         = $options['gmaps'] == 'on' ? 'checked' : '';
		$maps_api      = $options['maps_api'];

		$action_url    = $_SERVER['REQUEST_URI'];
		$imgpath       = plugins_url( '/img', __FILE__ );

		include('insights-options.php');

	}

}

endif;

if ( class_exists('WPInsights') ) :

	$WPInsights = new WPInsights();
	if ( isset( $WPInsights ) ) {
		register_activation_hook( __FILE__, array(&$WPInsights, 'install') );
	}

endif;
