<?php
/*
 * Plugin Name: Insights
 * Plugin URI: https://github.com/trepmal/Insights/
 * Description: Insights allows you to quickly search and insert information (links, images, videos, maps, news..) into your blog posts.
 * Version: 2.1
 * Author: Vladimir Prelovac / Kailey Lampert
 * Author URI: kaileylampert.com
 * License: GPLv2 or later
 * TextDomain: insights
 * DomainPath:
 * Network:
 */

// Avoid name collisions.
if ( !class_exists('WPInsights') ) :

class WPInsights {

	// Name for our options in the DB
	var $DB_option = 'WPInsights_options';

	// Initialize WordPress hooks
	function WPInsights() {

		// Add Options Page
		add_action( 'admin_menu',       array( $this, 'admin_menu' ) );

		add_action( 'wp_ajax_insights', array( $this, 'insights_cb' ) );

		// print scripts action
		add_action( 'admin_print_scripts-post.php',     array( $this, 'scripts_action' ) );
		add_action( 'admin_print_scripts-page.php',     array( $this, 'scripts_action' ) );
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'scripts_action' ) );
		add_action( 'admin_print_scripts-page-new.php', array( $this, 'scripts_action' ) );

	}

	function insights_cb() {
		include( 'insights-ajax.php' );
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
			'ajaxurl'              => admin_url( 'admin-ajax.php' ),
			'content_width'        => $content_width,
			'insights_url'         => plugins_url('', __FILE__),
			'insights_interactive' => $interactive,
			'nonce'                => $nonce,

			'txt_youtubelink'      => _x( 'Link', 'insights', 'link to youtube video' ),
			'txt_youtubeviews'     => _x( '<strong>Views:</strong> {views}', 'insights', '{views} is float placeholder' ),
			'txt_youtuberating'    => _x( '<strong>Rating:</strong> {rating}', 'insights', '{rating} is integer placeholder' ),
			'txt_youtubeadd'       => _x( 'Add Video', 'insights', 'add video to post content' ),
			'txt_youtubeoembed'    => __( 'Videos rely on oEmbed to play on the front-end', 'insights' ),

			'txt_insertlink'       => __( 'Insert link to selection', 'insights' ),
			'txt_nomatch'          => _x( 'No results matching "{query}"', 'insights', '{query} is search-phrase placeholder' ),
			'txt_ajaxerror'        => __( 'Cannot retrieve results', 'insights' ),
		) );

		if ( ! empty( $options['maps_api'] ) ) {
			wp_enqueue_script('googlemapsapi', '//maps.googleapis.com/maps/api/js?sensor=false', array(), 3, true );
			wp_enqueue_script('insightsmaps', plugins_url( '/js/insights-maps.js', __FILE__ ), array( 'insights', 'googlemapsapi'), 1, true);
			wp_localize_script('insights', 'InsightsMapSettings', array(
				'content_width' => $content_width,
				'v3api'         => $options['maps_api'],
				'geocode_fail'  => _x( 'Geocode was not successful for the following reason: {status}', 'insights', '{status} is error-code placeholder' ),
			) );
		}

	}

	function draw_insights() {
		$options = $this->get_options();
	?>
<p><?php _e( 'Enter keywords you would like to search for and press Search button.', 'insights' ); ?></p>
<input type="text" id="insights-search" name="insights-search" size="60" autocomplete="off"/>
<input id="insights-submit" class="button" type="button" value="<?php _e( 'Search', 'insights' ); ?>"  /> <br />

<label><input name="insights-radio" type="radio" checked="" value="1" /> <?php _e( 'My Blog',   'insights' ); ?></label>
<label><input name="insights-radio" type="radio" value="2"/> <?php             _e( 'Images',    'insights' ); ?></label>
<label><input name="insights-radio" type="radio" value="3"/> <?php             _e( 'Videos',    'insights' ); ?></label>
<label><input name="insights-radio" type="radio" value="4"/> <?php             _e( 'Wikipedia', 'insights' ); ?></label>
<label><input name="insights-radio" type="radio" value="6"/> <?php             _e( 'Google',    'insights' ); ?></label>
<label><input name="insights-radio" type="radio" value="7"/> <?php             _e( 'News',      'insights' ); ?></label>
<label><input name="insights-radio" type="radio" value="10"/> <?php            _e( 'Blogs',     'insights' ); ?></label>
<label><input name="insights-radio" type="radio" value="11"/> <?php            _e( 'Books',     'insights' ); ?></label>
<?php if ( ! empty( $options['maps_api'] ) ) : ?>
<label><input name="insights-radio" type="radio" value="5"/> <?php _e( 'Maps', 'insights' );   ?></label>
<?php endif; ?>

<div id="insights-results"></div>
	<div id="insights-map-all" style="display:none" >
		<p>
		<input class="button" type="button" value="<?php _e( 'Add Map', 'insights' );       ?>" onclick="insert_map();">
		<input class="button" type="button" value="<?php _e( 'Add Marker', 'insights' );    ?>" onclick="createMarkerAt();">
		<input class="button" type="button" value="<?php _e( 'Clear Markers', 'insights' ); ?>" onclick="clearMarkers();">
		<input class="button" type="button" value="<?php _e( 'Clear Path', 'insights' );    ?>" onclick="clearPolys();">
		</p>
	<div id="insights-map" style="height:450px; width:100%; padding:0px; margin:0px;"></div>
</div>
<?php

	}

	// Hook the options mage
	function admin_menu() {

		add_options_page( __( 'Insights Options', 'insights' ), __( 'Insights', 'insights' ), 'edit_pages', basename(__FILE__), array( $this, 'handle_options' ) );
		$show_on = apply_filters( 'insights_meta_box', array( 'post', 'page' ) );
		foreach( $show_on as $s ) {
			add_meta_box( 'WPInsights', __( 'Insights', 'insights' ), array( $this,'draw_insights' ), $s, 'normal', 'high' );
		}
	}

	// Handle our options
	function get_options() {
		$options = array(
			'post_results'  => 5,
			'image_results' => 16,
			'wiki_results'  => 10,
			'video_results' => 20,
			'image_tags'    => 'on',
			'image_text'    => 'on',
			'image_nonc'    => '',
			'interactive'   => '',
			'maps_api'      => ''
		);

		$saved = get_option( $this->DB_option, array() );

		$options = wp_parse_args( $saved, $options );

		if ( $saved != $options )
			update_option( $this->DB_option, $options );

		return $options;
	}

	// Set up everything
	function install() {
		$this->get_options();
	}

	function handle_options() {
		$options = $this->get_options();

		if ( isset( $_POST['submitted'] ) ) {

			check_admin_referer('insights');

			$options = array();

			$options['post_results']  = (int) $_POST['post_results'];
			$options['image_results'] = (int) $_POST['image_results'];
			$options['wiki_results']  = (int) $_POST['wiki_results'];
			$options['video_results'] = (int) $_POST['video_results'];
			$options['image_tags']    = isset( $_POST['image_tags'] )  ? 'on' : '';
			$options['image_nonc']    = isset( $_POST['image_nonc'] )  ? 'on' : '';
			$options['image_text']    = isset( $_POST['image_text'] )  ? 'on' : '';
			$options['interactive']   = isset( $_POST['interactive'] ) ? 'on' : '';
			$options['maps_api']      = $_POST['maps_api'];

			update_option( $this->DB_option, $options );
			echo '<div class="updated fade"><p>'. __( 'Plugin settings saved.', 'insights' ) .'</p></div>';
		}

		$post_results  = $options['post_results'];
		$image_results = $options['image_results'];
		$wiki_results  = $options['wiki_results'];
		$video_results = $options['video_results'];
		$image_tags    = $options['image_tags']  == 'on' ? 'checked' : '';
		$image_text    = $options['image_text']  == 'on' ? 'checked' : '';
		$image_nonc    = $options['image_nonc']  == 'on' ? 'checked' : '';
		$interactive   = $options['interactive'] == 'on' ? 'checked' : '';
		$maps_api      = $options['maps_api'];

		include('insights-options.php');

	}

}

endif;

if ( class_exists('WPInsights') ) :

	$WPInsights = new WPInsights();
	if ( isset( $WPInsights ) ) {
		register_activation_hook( __FILE__, array( $WPInsights, 'install' ) );
	}

endif;
