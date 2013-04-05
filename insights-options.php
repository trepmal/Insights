<div class="wrap" >
	<h2>Insights</h2>

	<form action="<?php echo $action_url ?>" method="post">

		<input type="hidden" name="submitted" value="1" />
		<?php wp_nonce_field('insights'); ?>

		<p>Enter the number of search results you would like to see for your searches.</p>
		<input type="text" name="post_results" size="15" value="<?php echo $post_results ?>"/><label for="post_results"> My blog</label><br/>
		<br />
		<input type="text" name="image_results" size="15" value="<?php echo $image_results ?>"/><label for="image_results"> Image</label><br/>
		<br />
		<input type="text" name="video_results" size="15" value="<?php echo $video_results ?>"/><label for="video_results"> Video</label><br/>
		<br />
		<input type="text" name="wiki_results" size="15" value="<?php echo $wiki_results ?>"/><label for="wiki_results"> Wikipedia</label><br/>
		<br />

		<label for="image_tags"><input type="checkbox" name="image_tags"  <?php echo $image_tags ?> /> Search Flickr images by tag</label>  <br />
		<label for="image_text"><input type="checkbox" name="image_text"  <?php echo $image_text ?> /> Search Flickr images by description</label>  <br />
		<label for="image_nonc"><input type="checkbox" name="image_nonc"  <?php echo $image_nonc ?> /> Search only non-commercial Flickr images</label>  <br />
		<label for="interactive"><input type="checkbox" name="interactive"  <?php echo $interactive ?> /> Show results as you type</label>  <br />

		<br />
		<h2>Google Maps</h2>
		<br />
		<!-- <input type="checkbox" name="gmaps"  <?php echo $gmaps ?> /><label for="gmaps"> Turn on Google Maps module</label>  <br />
		<br /> -->
		A Google Maps API key is required. You can get it free <a href="https://code.google.com/apis/console">here</a>.<br/>
		<input type="text" name="maps_api"  value="<?php echo $maps_api ?>"/><br />
		<br />

		<?php submit_button(); ?>

	</form>

</div>
type</label>  <br />
					
					<br />
					<h2>Google Maps</h2>								
					<br /> 				
					<input type="checkbox" name="gmaps"  <?php echo $gmaps ?> /><label for="gmaps"> Turn on Google Maps module</label>  <br />
					<br /> 				
					Enter your Google Maps API key. You can get it free <a href="http://code.google.com/apis/maps/signup.html">here</a>.<br/>
					<input type="text" name="maps_api" size="100" value="<?php echo $maps_api ?>"/><br />  					
					<br /> 				
																								
					<div class="submit"><input type="submit" name="Submit" value="Update" /></div>
			</form>
		</div>
				
	<br/><br/><h3>&nbsp;</h3>	
	 </div>

	</div>
	
<h5>WordPress plugin by <a href="http://www.prelovac.com/vladimir/">Vladimir Prelovac</a></h5>
</div>

