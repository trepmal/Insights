// Insights for WordPress plugin

function send_wp_editor( html1, html2, def ) {
	//if tinymce
		//if no selection
			//if no html2 insert at end
			//else use wrap around a default value
		//if selection
			//if no html2 replace selection
			//else wrap
	//else
		//if no selection
			//if no html2 insert at end
			//else use wrap around a default value
		//if selection
			//if no html2 replace selection
			//else wrap

	if ( typeof def == 'undefined' ) {
		def = '##';
	}

	if ((typeof tinyMCE != "undefined") && (edt = tinyMCE.getInstanceById('content')) && !edt.isHidden() ) {
		var sel = edt.selection.getContent({format : 'text'});

		if ( sel == '' ) { // if no selection

			if ( typeof html2 == 'undefined' ) {
				text = html1;
			} else {
				text = html1 + def + html2;
			}

			text = text.replace( /\n/g, '<br />' );

			tinyMCE.activeEditor.execCommand('mceInsertContent', false, text );

		} else { // if selection

			if ( typeof html2 == 'undefined' ) {
				text = html1;
			} else {
				text = html1 + sel + html2;
			}

			var win = window.dialogArguments || opener || parent || top;
			win.send_to_editor( text );

		}
	} else {
		// crazyness to wrap selection
		if ( typeof wpActiveEditor == 'undefined' )
			wpActiveEditor = 'content';

		// console.log( wpActiveEditor );
		canvas = document.getElementById( wpActiveEditor );

		if ( document.selection ) { //IE - NEEDS TESTING
			canvas.focus();
			sel = document.selection.createRange();

			if ( sel.text == '' )  { // if no selection

				if ( typeof html2 == 'undefined' ) {
					newtext = html1;
				} else {
					newtext = html1 + def + html2;
				}

				// selection = html_link;
			} else { // if selection
				if ( typeof html2 == 'undefined' ) {
					newtext = html1;
				} else {
					newtext = html1 + sel.text + html2;
				}

			}

			sel.text = newtext;
			canvas.focus();
		} else if ( canvas.selectionStart || canvas.selectionStart == '0' ) { // FF, WebKit, Opera
			canvas.selectionStart
			text = canvas.value;
			startPos = canvas.selectionStart;
			endPos = canvas.selectionEnd;
			scrollTop = canvas.scrollTop;

			selection = text.substring( startPos, endPos );
			if ( selection == '' )  { // if no selection

				if ( typeof html2 == 'undefined' ) {
					newtext = html1;
				} else {
					newtext = html1 + def + html2;
				}

				// selection = html_link;
			} else { // if selection
				if ( typeof html2 == 'undefined' ) {
					newtext = html1;
				} else {
					newtext = html1 + selection + html2;
				}

			}

			canvas.value = text.substring(0, startPos) + newtext + text.substring(endPos, text.length);

			canvas.focus();
			canvas.selectionStart = startPos + content.length;
			canvas.selectionEnd = startPos + content.length;
			canvas.scrollTop = scrollTop;
		} else {

				if ( typeof html2 == 'undefined' ) {
					newtext = html1;
				} else {
					newtext = html1 + def + html2;
				}

			canvas.value += newtext;
			canvas.focus();
		}
	}
}
function insert_link(html_link) {
	send_wp_editor( '<a href="' + html_link + '">', '</a>', html_link );
}

function insert_image(link, src, title) {
	var size = document.getElementById('img_size').value;
	var img = '<a href="' + link + '"><img src="' + src + size + '.jpg" alt="' + title.replace(/\"/g, "'") + '" title="' + title.replace(/\"/g, "'") + '" hspace="5" border="0" /></a>';

	send_wp_editor( img );
}


var videoid = 0;

function insert_video() {
	// take advantage of oembed
	var video = "\n" + 'http://www.youtube.com/watch?v=' + videoid + "\n";

	send_wp_editor( video );
}

function insert_map() {
	var maphtml = '<img src="' + updateImage() + '" alt="" />';

	send_wp_editor( maphtml );
}

function show_video(ytfile, yttitle, ytdesc, ytviews, ytrating) {

	videoid=ytfile;
	var link='<span style="padding: 2px"><object type="application/x-shockwave-flash" width="425" height="344" data="//www.youtube.com/v/'+ytfile+'&amp;rel=0&amp;fs=1"><param name="movie" value="//www.youtube.com/v/'+ytfile+'&amp;rel=0&amp;fs=1"></param><param name="allowFullScreen" value="true"></param><param name="wmode" value="transparent" /></object></span>';
	var data='<h4>'+yttitle+'</h4><p><a href="http://www.youtube.com/watch?v='+ytfile+'">link</a></p><p>'+ytdesc+'</p><p><strong>Views:</strong> '+ytviews+'</p><p><strong>Rating: </strong>'+ytrating+'</p>';
	var button='<br /><p><input class="button" type="button" value="Add Video" onclick="insert_video();" ></p><p>(you may need to go from Visual to HTML mode and back to see the video object)</p>';

	jQuery('#insights-youtube-preview').html(link);
	jQuery('#insights-youtube-data').html(data+button);
	jQuery('#insights-youtube-holder').fadeIn();
}


// setup everything when document is ready
jQuery(document).ready(function($) {

	// initialize the variables
	var search_timeout = undefined;
	var last_mode = undefined;
	var last_search = undefined;

	function show_results(output, mode) {
		var curr_mode = $("input[name='insights-radio']:checked").val();
		if (mode==curr_mode)
			$('#insights-results').html(output);
		else
			$('#insights-results').html('');
	}

	function submit_me() {

		// check if the search string is empty
		if ($('#insights-search').val().length == 0) {
			$('#insights-results').html('');
			return;
		}

		// get the search phrase
		var phrase = $('#insights-search').val();

		// get active radio checkbox
		var mode = $("input[name='insights-radio']:checked").val();


		if (mode == 5) { // maps
			$('#insights-results').html('');
			$('#insights-map-all').fadeIn(600);

			if (!map) {
				init_map();
			}
			else {
				showAddress();
			}

			last_mode = mode;
				last_search = phrase;

			return;
		} else {
			$('#insights-map-all').fadeOut(500);
		}

		if ((jQuery.trim(phrase) == last_search) && last_mode == mode) {
			return;
		}
		last_mode = mode;
		last_search = phrase;

		$('#insights-results').html('<img src="' + InsightsSettings.insights_url + '/img/loading.gif" />');

		if (mode==4) // wikipedia
		{

			$.getJSON('//en.wikipedia.org/w/api.php?action=query&list=search&srwhat=text&srlimit=10&srsearch='+escape(phrase)+'&format=json&callback=?',
				function(data){
					var output='';
					var wikipediaUrl = "http://en.wikipedia.org/wiki/";
					if (!data.query.search.length)
						output='No results matching "'+phrase+'".';
					else
						$.each(data.query.search, function(i,item){
							output = output+'<p><a  target="_blank" style="text-decoration:none;" href="'+ wikipediaUrl + item.title.replace(/ /g, "_")+'" ><strong>'+item.title+ '</strong></a> <img title="Insert link to selection" style="cursor:pointer;" onclick="insert_link(\''+wikipediaUrl + item.title.replace(/ /g, "_")+'\');" src="'+InsightsSettings.insights_url+'/img/link.png" /></p>';
						});
					show_results(output, mode);
				 });
			return;
		}


		if (mode==6)  // google
		{
			$.getJSON("//ajax.googleapis.com/ajax/services/search/web?q="+escape(phrase)+"&v=1.0&rsz=large&callback=?",
				function(data){
					var output='';
					if (!data.responseData.results.length)
						output='No results matching "'+phrase+'".';
					else
						$.each(data.responseData.results, function(i,item){
							output=output+'<p><a  target="_blank" style="text-decoration:none;" href="'+item.url+'"><strong>'+ item.titleNoFormatting+'</strong></a> <img style="cursor:pointer;" title="Insert link to selection" onclick="insert_link(\''+item.url+'\');" src="'+InsightsSettings.insights_url+'/img/link.png" /><p>'+item.content+'</p></p>';
						});

					show_results(output, mode);
				});

			return;
		}

		if (mode==7)  // news
		{
			$.getJSON("//ajax.googleapis.com/ajax/services/search/news?q="+escape(phrase)+"&v=1.0&scoring=d&rsz=large&callback=?",
				function(data){
					var output='';
					if (!data.responseData.results.length)
						output='No results matching "'+phrase+'".';
					else
						$.each(data.responseData.results, function(i,item){
							output=output+'<p><a  target="_blank" style="text-decoration:none;" href="'+item.unescapedUrl+'"><strong>'+ item.titleNoFormatting+'</strong></a> <img style="cursor:pointer;" title="Insert link to selection" onclick="insert_link(\''+item.url+'\');" src="'+InsightsSettings.insights_url+'/img/link.png" /><br />'+item.publisher+', '+item.location+' on '+item.publishedDate +'<p>'+item.content+'</p></p>';
						});

					show_results(output, mode);
				});

			return;
		}


		if (mode==10)  // blogs
		{
			$.getJSON("//ajax.googleapis.com/ajax/services/search/blogs?q="+escape(phrase)+"&v=1.0&scoring=d&rsz=large&callback=?",
				function(data){
					var output='';
					if (!data.responseData.results.length)
						output='No results matching "'+phrase+'".';
					else
						$.each(data.responseData.results, function(i,item){
							output=output+'<p><a  target="_blank" style="text-decoration:none;" href="'+item.postUrl+'"><strong>'+ item.titleNoFormatting+'</strong></a> <img style="cursor:pointer;" title="Insert link to selection" onclick="insert_link(\''+item.postUrl+'\');" src="'+InsightsSettings.insights_url+'/img/link.png" /><br />'+item.blogUrl+'<p>'+item.content+'</p></p>';
						});

					show_results(output, mode);
				});

			return;
		}


		if (mode==11)  // books
		{
			$.getJSON("//ajax.googleapis.com/ajax/services/search/books?q="+escape(phrase)+"&v=1.0&as_brr=1&rsz=large&callback=?",
				function(data){
					var output='';
					if (!data.responseData.results.length)
						output='No results matching "'+phrase+'".';
					else
						$.each(data.responseData.results, function(i,item){
							output=output+'<p><a target="_blank" style="text-decoration:none;" href="'+item.unescapedUrl+'"><strong>'+ item.titleNoFormatting+'</strong></a> <img style="cursor:pointer;" title="Insert link to selection" onclick="insert_link(\''+item.unescapedUrl+'\');" src="'+InsightsSettings.insights_url+'/img/link.png" /><p>'+ item.authors+', published '+item.publishedYear+', '+item.pageCount+' pages </p></p>';
						});

					show_results(output, mode);
				});

			return;
		}

		// create the query
		// var query = InsightsSettings.insights_url + '/insights-ajax.php?search=' + escape(phrase) + '&mode=' + mode;
		var query = InsightsSettings.ajaxurl + '?action=insights&search=' + escape(phrase) + '&mode=' + mode;

		var cached = $.jCache.getItem(query);

		if (cached)
				show_results(cached, mode);
		else
		{
			var apiParams = {
				search: phrase,
				mode: mode,
				action: 'insights',
				_ajax_nonce:InsightsSettings.nonce
			};

			$.ajax( InsightsSettings.ajaxurl, {
				type: "GET",
				data: apiParams,
				datatype: "string",
				error: function() {
					$('#insights-results').html('Can not retrieve results');
				},
				success: function(searchReponse) {

					show_results(searchReponse, mode);
					$.jCache.setItem(query, searchReponse);

				}
			});
		}

	}

	$('#insights-submit').click(function() {
		submit_me();
	});

	// check for ENTER or ArrowDown keys
	$('#insights-search').keypress(function(e) {
		if (e.keyCode == 13 || e.keyCode == 40) {
			submit_me();
			return false;
		}

	});

	if (parseInt(InsightsSettings.insights_interactive))

	// automatically refresh the view
	$('#insights-search').keyup(function(e) {
		if (search_timeout != undefined) {
			clearTimeout(search_timeout);
		}
		if ($('#insights-search').val().length < 3) {
			return;
		}

		search_timeout = setTimeout(function() {
			search_timeout = undefined;
			submit_me();
		},
		700);
	});

});