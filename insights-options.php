<div class="wrap" >
	<h2>Insights</h2>
				
	<div id="poststuff" style="margin-top:10px;">
		<div id="sideblock" style="float:right;width:270px;margin-left:10px;"> 

		 <iframe width=270 height=800 frameborder="0" src="http://www.prelovac.com/plugin/news.php?id=8&utm_source=plugin&utm_medium=plugin&utm_campaign=Insights"></iframe>

 	</div>

	 <div id="mainblock" style="width:710px">
	 
		<div class="dbx-content">
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
					
					<input type="checkbox" name="image_tags"  <?php echo $image_tags ?> /><label for="image_tags"> Search Flickr images by tag</label>  <br />
					<input type="checkbox" name="image_text"  <?php echo $image_text ?> /><label for="image_text"> Search Flickr images by description</label>  <br />
					<input type="checkbox" name="image_nonc"  <?php echo $image_nonc ?> /><label for="image_nonc"> Search only non-commercial Flickr images</label>  <br />
					<input type="checkbox" name="interactive"  <?php echo $interactive ?> /><label for="interactive"> Show results as you type</label>  <br />
					
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

