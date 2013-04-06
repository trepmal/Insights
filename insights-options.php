<div class="wrap" >
	<h2><?php _e( 'Insights', 'insights' ); ?></h2>

	<form action="<?php echo add_query_arg('',''); ?>" method="post">

		<input type="hidden" name="submitted" value="1" />
		<?php wp_nonce_field('insights'); ?>

		<p><?php _e( 'Enter the number of search results you would like to see for your searches.', 'insights' ); ?></p>
		<p><input type="text" name="post_results" id="post_results" size="15" value="<?php echo $post_results ?>" /><label for="post_results"> <?php _e( 'My blog', 'insights' ); ?></label></p>
		<p><input type="text" name="image_results" id="image_results" size="15" value="<?php echo $image_results ?>" /><label for="image_results"> <?php _e( 'Image', 'insights' ); ?></label></p>
		<p><input type="text" name="video_results" id="video_results" size="15" value="<?php echo $video_results ?>" /><label for="video_results"> <?php _e( 'Video', 'insights' ); ?></label></p>
		<p><input type="text" name="wiki_results" id="wiki_results" size="15" value="<?php echo $wiki_results ?>" /><label for="wiki_results"> <?php _e( 'Wikipedia', 'insights' ); ?></label></p>

		<p><label for="image_tags"><input type="checkbox" id="image_tags" name="image_tags" <?php echo $image_tags ?> /> <?php _e( 'Search Flickr images by tag', 'insights' ); ?></label></p>
		<p><label for="image_text"><input type="checkbox" id="image_text" name="image_text" <?php echo $image_text ?> /> <?php _e( 'Search Flickr images by description', 'insights' ); ?></label></p>
		<p><label for="image_nonc"><input type="checkbox" id="image_nonc" name="image_nonc" <?php echo $image_nonc ?> /> <?php _e( 'Search only non-commercial Flickr images', 'insights' ); ?></label></p>
		<p><label for="interactive"><input type="checkbox" id="interactive" name="interactive" <?php echo $interactive ?> /> <?php _e( 'Show results as you type', 'insights' ); ?></label></p>

		<h2><?php _e( 'Google Maps', 'insights' ); ?></h2>

		<p><label><input type="text" name="maps_api" value="<?php echo $maps_api ?>" /> <?php _e( 'A Google Maps API key is required. You can get it free <a href="https://code.google.com/apis/console">here</a>', 'insights' ); ?></label></p>

		<?php submit_button(); ?>

	</form>

</div>