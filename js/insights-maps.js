var map = null;
var geocoder = new google.maps.Geocoder();

var editingNow = false;
var polys = [];
var current_poly = null;
var markers = [];

var insights_map_id = 'insights-map';

function createMarkerAt() {
	marker = new google.maps.Marker({
		position: map.getCenter(),
		map: map,
		title: "Drag Me!",
		draggable: true
	});
	marker.setDraggable(true);
	markers.push(marker);
}

function clearMarkers() {
	if (markers) {
		for (i in markers) {
			markers[i].setMap(null);
		}
	}
	markers = [];
}

function clearPolys() {
	if (polys) {
		for (i in polys) {
			polys[i].setMap(null);
		}
	}
	current_poly = null;
	polys = [];
}


function updateImage() {
	api = InsightsMapSettings.v3api;
	var baseUrl = "http://maps.googleapis.com/maps/api/staticmap?";

	var params = [];
	params.push("center=" + map.getCenter().lat().toFixed(6) + "," + map.getCenter().lng().toFixed(6));

	var markerSize = '';
	var markerColor = 'red';
	var markerLetter = '';
	var markerParams = markerSize + markerColor + markerLetter;
	var markersArray = [];
	for (var i = 0; i < markers.length; i++) {
		markersArray.push( 'color: '+ markerColor + '%7C' + markers[i].getPosition().lat().toFixed(6) + "," + markers[i].getPosition().lng().toFixed(6) );
	}

	//	 markersArray.push(marker.getLatLng().lat().toFixed(6) + "," + marker.getLatLng().lng().toFixed(6) + "," + markerParams);
	if (markersArray.length) params.push("markers=" + markersArray.join("|"));

	// if ( polys != null ) {
		var polyParams = "color:blue,weight:5|";
	for (var i = 0; i < polys.length; i++) {

		theline = polys[i].getPath().getArray().toString();
		theline = theline.replace(/\),\(/g, '|');
		theline = theline.replace(/, /g, ',');
		theline = theline.replace(/\(/g, '');
		theline = theline.replace(/\)/g, '');

		params.push("path=" + polyParams + theline );
	}
	params.push("zoom=" + map.getZoom());
	params.push("size=" + 480 + "x" + 300);

	var ret = baseUrl + params.join("&") + "&sensor=false&key="+api;

	return ret;
}

function showAddress() {
	var searchField = document.getElementById("insights-search");

	var address = searchField.value;

		geocoder.geocode( { 'address': address }, function(results, status) {

		if ( status == google.maps.GeocoderStatus.OK ) {
				map.setCenter( results[0].geometry.location );
				// do_marker( map, results[0].geometry.location );
			} else {
				alert("Geocode was not successful for the following reason: " + status + "\nDid you provide a city and state?");
			}

		});
}
function init_map() {

		var myLatlng = new google.maps.LatLng( '48.168375', '-123.475486' );
		var options = {
			zoom: 10,
			center: myLatlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map = new google.maps.Map(document.getElementById(insights_map_id), options );


		geocoder.geocode( { 'address': document.getElementById("insights-search").value }, function(results, status) {

			if ( status == google.maps.GeocoderStatus.OK ) {
				map.setCenter( results[0].geometry.location );
			} else {
				alert("Geocode was not successful for the following reason: " + status + "\nDid you provide a city and state?");
			}

		});

		var polyOptions = {
			strokeColor: '#000000',
			strokeOpacity: 1.0,
			strokeWeight: 3
		}

		google.maps.event.addListener( map, 'click', function( event ) {
			if ( current_poly == null ) {
		        poly = new google.maps.Polyline(polyOptions);
		        poly.setMap(map);
		        current_poly = polys.length;
		        polys.push(poly);
			} else {
				poly = polys[ current_poly ];
			}
	        var path = poly.getPath();

	        // Because path is an MVCArray, we can simply append a new coordinate
	        // and it will automatically appear
	        path.push(event.latLng);

		});

		google.maps.event.addListener( map, 'rightclick', function( event ) {
			current_poly = null;
		});
}
