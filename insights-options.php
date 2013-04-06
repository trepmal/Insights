<div class="wrap" >
	<h2>Insights</h2>

	<form action="<?php echo $action_url ?>" method="post">

		<input type="hidden" name="submitted" value="1" />
		<?php wp_nonce_field('insights'); ?>

		<p>Enter the number of search results you would like to see for your searches.</p>
		<p><input type="text" name="post_results" id="post_results" size="15" value="<?php echo $post_results ?>" /><label for="post_results"> My blog</label></p>
		<p><input type="text" name="image_results" id="image_results" size="15" value="<?php echo $image_results ?>" /><label for="image_results"> Image</label></p>
		<p><input type="text" name="video_results" id="video_results" size="15" value="<?php echo $video_results ?>" /><label for="video_results"> Video</label></p>
		<p><input type="text" name="wiki_results" id="wiki_results" size="15" value="<?php echo $wiki_results ?>" /><label for="wiki_results"> Wikipedia</label></p>

		<p><label for="image_tags"><input type="checkbox" id="image_tags" name="image_tags" <?php echo $image_tags ?> /> Search Flickr images by tag</label></p>
		<p><label for="image_text"><input type="checkbox" id="image_text" name="image_text" <?php echo $image_text ?> /> Search Flickr images by description</label></p>
		<p><label for="image_nonc"><input type="checkbox" id="image_nonc" name="image_nonc" <?php echo $image_nonc ?> /> Search only non-commercial Flickr images</label></p>
		<p><label for="interactive"><input type="checkbox" id="interactive" name="interactive" <?php echo $interactive ?> /> Show results as you type</label></p>

		<h2>Google Maps</h2>

		<p><label><input type="text" name="maps_api" value="<?php echo $maps_api ?>" /> A Google Maps API key is required. You can get it free <a href="https://code.google.com/apis/console">here</a></label></p>

		<?php submit_button(); ?>

	</form>

</div>