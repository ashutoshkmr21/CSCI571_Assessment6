<?php
include 'geo_hash.php'
?>
<?php
	$keyword_error = $custom_loc_error = $from_loc = $distance_error = "";
	$valid = 0;
	$obj = null;
	$str = "";
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST["search_keyword"])) {
		$searh_key = $_POST["search_keyword"];
		if(empty($searh_key)) {
			$keyword_error = "Enter Value, this can't be blank.";
			$valid++;
		}
		$from_loc = $_POST["from_location"];
		if($from_loc == "custom_loc") {
			$from_loc = $_POST["custom_location"];
		}
		if(empty($from_loc)) {
			$custom_loc_error = "Please enter reference location";
			$valid++;
		}
		$distance = $_POST["distance_in_miles"];
		if (!preg_match("/^[0-9]/",$distance))
       {
       $distance_error = "Only numbers allowed";
        $valid++;	   
       }
       if($valid == 0) {
       		$category = $_POST["category_list"];
       		$lat = $_POST["location_lat"];
       		$lon = $_POST["location_lon"];
       		if($from_loc != "here") {
       			$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($from_loc)."&key=AIzaSyAmOJZ9uWZmpdms5RC0OCVPhCQuWgLantQ";
				$json = file_get_contents($url);
				$obj = json_decode($json, JSON_PRETTY_PRINT);
				$lat = $obj['results'][0]['geometry']['location']['lat'];
				$lon = $obj['results'][0]['geometry']['location']['lng'];
       		} 
       		$geoPoint = encode($lat, $lon);
       		$url = "https://app.ticketmaster.com/discovery/v2/events.json?keyword=".urlencode($searh_key)."&geoPoint=".urlencode($geoPoint)."&radius=".urlencode($distance)."&segmentId=".urlencode($category)."&unit=miles&apikey=ZCO9IUNBfaAyVGbG0do4Ok9v2NJHMnQQ";

			$json = file_get_contents($url);
			$obj = json_encode(json_decode($json, JSON_PRETTY_PRINT));
			$obj = json_decode($json);
			$obj = $obj->_embedded->events;
			$str = $lat.','.$lon.'----';       		
       }
	}
	exit($str.json_encode($obj));
}

if(isset($_REQUEST['eventid'])) {
	$url = "https://app.ticketmaster.com/discovery/v2/events/".urlencode($_REQUEST['eventid'])."?apikey=ZCO9IUNBfaAyVGbG0do4Ok9v2NJHMnQQ";
	$json = file_get_contents($url);
	$obj = json_decode($json);
	exit(json_encode($obj));
}

if(isset($_REQUEST['location'])) {
	$url = "https://app.ticketmaster.com/discovery/v2/venues?apikey=ZCO9IUNBfaAyVGbG0do4Ok9v2NJHMnQQ&keyword=".urlencode($_REQUEST['location']);
	$json = file_get_contents($url);
	$obj = json_decode($json);
	exit(json_encode($obj));
}

?>

<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		h1 {
			text-align: center;
			margin: 0px;
		}
		#searchbox_container {
			width: 615px;
			height: 240px;
			left: 405px;
			position: relative;
			border: 2px solid black;
			background-color: #F9F9F9;
		}

		#searchbox_container h4 {
			text-align: center;
		}

		#search_keyword {
			position: relative;
			top: -19px;
			left: 70px;
		}
		#from_location_div {
			float: right;
			position: relative;
			right: 70px;
			top: -20px;
		}
		#category_list {
			position: relative;
			top: -18px;
			left: 70px;
		}
		#distance_in_miles {
			position: relative;
			top: -20px;
			left: 118px;
		}
		#current_loc {
			position: relative;
			top: -53px;
			left: 56px;
		}
		#here_loc {
			position: relative;
			top: -17px;
			left: 32px;
		}
		#new_loc {
			position: relative;
			left: 32px;
			top: -73px;
		}
		#custom_location {
			position: relative;
			left: 35px;
			top: -75px;
		}
		#search_button {
			position: relative;
			left: -121px;
			width: 70px;
			top: 50px;
		}
		#clear_button {
			position: relative;
			left: -93px;
			width: 70px;
			top: 50px;
		}
		ul {
			list-style: none;
		}

		#searchbox ul {
			margin: 0px;
		}

		#content_section {
			width: 925px;
			position: relative;
			left: 289px;
			text-align: center;
		}
		#content_section table {
			width: 800px;
		}

		#content_section li {
			display: inline;
		}

		#content_section h3 {
			text-align: center;
			position: relative;
			/*left: 100px;*/
			width: 100%;
			white-space: nowrap;
			display: inline-block;
			margin-top: 0px;
		}

		#event_details_content {
			width: 400px;
			position: relative;
			display: inline-block;
			left: 0;
			text-align: left;
		}

		#event_details_content h3 {
			text-align: center;
		}

		#event_details_content div {
			width: 400px;
		}

		#event_details_content ul {
			list-style: none;
			padding-left: 0px;
			margin-top: 2px;
		}

		#event_details_content p {
			margin-top: 3px;
		}

		a {
			text-decoration: none;
			color: black;
		}
		input[type=number]::-webkit-inner-spin-button, 
		input[type=number]::-webkit-outer-spin-button { 
  			-webkit-appearance: none; 
  			margin: 0; 
		}

		table, table th, table td{
			border: 2px solid;
		}

		table {
			border-collapse: collapse;
			position: relative;
			left: 30px;
			top: 32px;
		}

		#venue_details_table {
			left: 0px;
		}

		table th, table tr {
			height: 40px;
		}

		#venue_map_parent {
			height: 400px;
			width: 650px;
		}

		#google_maps, #embedded_venue_map {
			height: 400px;
			width: 500px;
		}

		#embedded_venue_map {
			position: center;
		}

		#venue_map_parent ul, #eventlist_page_map ul {
			list-style: none;
			width: 100px;
			margin: 0px;
			padding: 0px;
			clear: both;
			float: left;
			position: relative;
			text-align: center;
			top: 170px;
		}
		#no_records {
			border: 1px solid black;
			width: 400px;
			text-align: center;
			height: 26px;
			background-color: grey;
			position: absolute;
			left: 233px;
		}
		#eventlist_page_map {
			width: 98px;
			height: 85px;
			background-color: #F9F9F9;
			position: absolute;
		}
		#eventlist_page_map ul {
			position: absolute;
			top: 0px;
			background-color: #F9F9F9;
		}

		#eventlist_page_map li {
			margin-top: 3px;
			height: 25px;
		}

		#eventlist_page_map li:hover {
			background-color: #D9D9D9;
		}

		#venue_map_display_img, #venue_img_display_img {
			width: 30px;
			height: 20px;
		}
		/*#seatmap_div {
			position: absolute;
			top: 43px;
			right: 25px;
			width: 500px;
		}*/

		#seatmap_div {
			position: relative;
			width: 500px;
			display: inline-block;
			left: 0;
			/*bottom: 60px;*/
		}

		#seatmap_div img {
			width: 100%;
			height: 100%;
		}
		#venue_details_content {
			text-align: center;
			width: 861px;
			left: 294px;
			position: relative;
			top: 44px;
			margin-bottom: 75px;
		}
		#venue_image {
			width: 861px;
			text-align: center;
			left: 294px;
			position: relative;
			top: 100pxpx;
		}
		#venue_img_container{
			width: 861px;
		}
		#venue_img_container img {
			width: 100%;
		}

		a:hover {
			color: grey;
			cursor: pointer;
		}
		/*ul li:hover,*/ #artist_div_content li:hover {
			color:grey;
			cursor: pointer;
		}

		#venue_map_parent li:hover {
			color: grey;
		}

		.event_name:hover, .venue_name:hover {
			color: grey;
			cursor: pointer;
		}
		/*#eventlist_page_map ul li:hover {
			color: white;
		}*/
		#google_maps_popup {
			display: none;
			position: absolute;
			left: 100px;
			top: 50px;
			border: 1px solid black;
			padding: 10px;
			text-align: justify;
			width: 325px;
			height: 245px;
		}
		#venue_img_display_node, #venue_map_display {
			color: grey;
		}
	</style>
	<script>
		function showMapPositioned(event, lati, long, ele) {
			var ele_, x_cord, y_cord;
			var temp_div= "google_maps_popup";
			ele_ = document.getElementById('google_maps_popup');
			const rect = event.getBoundingClientRect();
			x_cord = rect.left + window.scrollX;;
			y_cord = rect.top + window.scrollY;
			x_cord -= 10; 
			y_cord +=  17;
			ele_.style.left = x_cord + "px";
			ele_.style.top = y_cord + "px";
			ele_.style.display = "block";
			showMap(lati, long, temp_div);
			return;
		}
	</script>

	<script type="text/javascript">
		function setDirectionDiv(lati, lang, ele) {
			var map_div = document.getElementById(ele);
			var super_col2 = document.createElement("div");
			super_col2.setAttribute("id", "eventlist_page_map");
			var modes_container = document.createElement("ul");
			var mode1 = document.createElement("li");
			nametext = document.createTextNode("Walk There");
			mode1.appendChild(nametext);
			mode1.onclick = function() {initMap(lati, lang, ele, 'WALKING', true)};
			modes_container.appendChild(mode1);

			var mode2 = document.createElement("li");
			nametext = document.createTextNode("Bike There");
			mode2.appendChild(nametext);
			mode2.onclick = function(){initMap(lati, lang, ele, 'BICYCLING', true);}
			modes_container.appendChild(mode2);

			var mode3 = document.createElement("li");
			nametext = document.createTextNode("Drive There");
			mode3.appendChild(nametext);
			mode3.onclick = function(){initMap(lati, lang, ele, 'DRIVING', true)};
			modes_container.appendChild(mode3);

			super_col2.appendChild(modes_container);
			map_div.appendChild(super_col2);
		}

	</script>
	<script>
		// Initialize and add the map
		function showMap(lati, lang, ele) {
			// The location of Uluru
		  var loc = {lat: lati, lng: lang};
		  // The map, centered at Uluru
		  var map = new google.maps.Map(
		      document.getElementById(ele), {zoom: 12, center: loc});
		  // The marker, positioned at Uluru
		  var marker = new google.maps.Marker({position: loc, map: map});
		  marker.setMap(map);
		}

		function initMap(lati, lang, ele, mode, isEventListMap=false) {

			var directionsService = new google.maps.DirectionsService;
        	var directionsDisplay = new google.maps.DirectionsRenderer;		
        	var map = new google.maps.Map(document.getElementById(ele), {
          		zoom: 12,
          		center: {lat: lati, lng: lang}
        		});
        	directionsDisplay.setMap(map);
        	directionsService.route({
	        origin: new google.maps.LatLng(Number(document.getElementById('location_lat').value), Number(document.getElementById('location_lon').value)),
	        destination: new google.maps.LatLng(lati, lang),
	        travelMode: mode
	        //DRIVING, WALKING, BICYCLING, TRANSIT
	        }, function(response, status) {
	          if (status === 'OK') {
	            directionsDisplay.setDirections(response);
	          } else {
	            window.alert('Directions request failed due to ' + status);
	          }
	        });

	        if(isEventListMap) {
	        	setDirectionDiv(lati, lang, ele);
	        }
		}

		function displayMapsDirectionModes(lati, long, ele, dest, mode) {
			var directionsService = new google.maps.DirectionsService;
        	var directionsDisplay = new google.maps.DirectionsRenderer;

        	directionsService.route({
	        origin: new google.maps.LatLng(Number(document.getElementById('location_lat').value), Number(document.getElementById('location_lon').value)),
	        destination: new google.maps.LatLng(lati, lang),
	        travelMode: mode
	        //DRIVING, WALKING, BICYCLING, TRANSIT
	        }, function(response, status) {
	          if (status === 'OK') {
	            directionsDisplay.setDirections(response);
	          } else {
	            window.alert('Directions request failed due to ' + status);
	          }
	        });
		}
	</script>	 
	<script type="text/javascript">
		var event_names = document.getElementsByClassName("event_name");

		function removeContent(parent) {
			while (parent.firstChild) {
   				parent.removeChild(parent.firstChild);
			}
		}

		function clearAll(disabledCustomLoc) {
			var ele = document.getElementById("google_maps_popup");
			if(ele.style.display=="block"){
				ele.style.display= "none";	
			}
			removeContent(document.getElementById("content_section"));
			removeContent(document.getElementById("venue_details_content"));
			removeContent(document.getElementById("venue_image"));
			removeContent(document.getElementById("google_maps_popup"));
			if(disabledCustomLoc) {
				document.getElementById("custom_location").disabled = true;
				getDetails();
			}
		}

		function populateEventDetails(resp) {

			var table_div = document.getElementById("content_section");
			clearAll(false);

			var event_details_div = document.createElement("div");
			event_details_div.setAttribute("id", "event_details_content");
			var nameHeader = document.createElement("h3");
			nameHeader.appendChild(document.createTextNode(resp['name']));
			table_div.appendChild(nameHeader);

			var date_div = document.createElement("div");
			var date_header = document.createElement("b");
			var date_content = document.createElement("p");
			date_content.appendChild(document.createTextNode(resp['dates']['start']['localDate'] + ' ' + resp['dates']['start']['localTime']));
			date_header.appendChild(document.createTextNode("Date"));
			date_div.appendChild(date_header);
			date_div.appendChild(date_content);
			event_details_div.appendChild(date_div);

			if(resp['_embedded']['attractions'] && resp['_embedded']['attractions'].length > 0) {
				var artist_div = document.createElement("div");
				artist_div.setAttribute("id", "artist_div_content");
				var artist_header = document.createElement("b");
				var artist_content = document.createElement("ul");

				for(var artist_count=0; artist_count < resp['_embedded']['attractions'].length; artist_count++) {
					var artist1 = document.createElement("li");
					var link1 = document.createElement("a");
					link1.href = resp['_embedded']['attractions'][artist_count]['url'];
					link1.setAttribute("target", "_blank");
					link1.appendChild(document.createTextNode(resp['_embedded']['attractions'][artist_count]['name']));
					artist1.appendChild(link1);
					artist_content.appendChild(artist1);
					if(artist_count != resp['_embedded']['attractions'].length-1) {
						artist_content.appendChild(document.createTextNode(" | "));
					}
				}
				artist_header.appendChild(document.createTextNode("Artist / Team"));
				artist_div.appendChild(artist_header);
				artist_div.appendChild(artist_content);
				event_details_div.appendChild(artist_div);
			}	

			if(resp['_embedded']['venues'] && resp['_embedded']['venues'][0]['name']) {
				var venue_div = document.createElement("div");
				var venue_header = document.createElement("b");
				var venue_content = document.createElement("p");
				venue_content.appendChild(document.createTextNode(resp['_embedded']['venues'][0]['name']));
				venue_header.appendChild(document.createTextNode("Venue"));
				venue_div.appendChild(venue_header);
				venue_div.appendChild(venue_content);
				event_details_div.appendChild(venue_div);
			}

			var genre_div = document.createElement("div");
			var genre_header = document.createElement("b");
			var genre_content = document.createElement("ul");
			var genre_count = 0;
			if(resp["classifications"] && resp["classifications"][0] && resp["classifications"][0]["segment"] && resp["classifications"][0]["segment"]["name"] && resp["classifications"][0]["segment"]["name"] != "Undefined") {
				var genre1 = document.createElement("li");
				genre1.appendChild(document.createTextNode(resp["classifications"][0]["segment"]["name"]));
				genre_content.appendChild(genre1);
				genre_count++;
			}
			if(resp["classifications"] && resp["classifications"][0] && resp["classifications"][0]["genre"] && resp["classifications"][0]["genre"]["name"] && resp["classifications"][0]["genre"]["name"] != 'Undefined'){
				var genre2 = document.createElement("li");
				genre2.appendChild(document.createTextNode(resp["classifications"][0]["genre"]["name"]));
				genre_content.appendChild(document.createTextNode(" | "));
				genre_content.appendChild(genre2);
				genre_count++;
			}
			if(resp["classifications"] && resp["classifications"][0] && resp["classifications"][0]["subGenre"] && resp["classifications"][0]["subGenre"]["name"] && resp["classifications"][0]["subGenre"]["name"] != 'Undefined'){
				var genre3 = document.createElement("li");
				genre3.appendChild(document.createTextNode(resp["classifications"][0]["subGenre"]["name"]));
				genre_content.appendChild(document.createTextNode(" | "));
				genre_content.appendChild(genre3);
				genre_count++;
			}
			if(resp["classifications"] && resp["classifications"][0] && resp["classifications"][0]["type"] && resp["classifications"][0]["type"]["name"] && resp["classifications"][0]["type"]["name"]!='Undefined'){
				var genre4 = document.createElement("li");
				genre4.appendChild(document.createTextNode(resp["classifications"][0]["type"]["name"]));
				genre_content.appendChild(document.createTextNode(" | "));
				genre_content.appendChild(genre4);
				genre_count++;
			}
			if(resp["classifications"] && resp["classifications"][0] && resp["classifications"][0]["subType"] && resp["classifications"][0]["subType"]["name"] && resp["classifications"][0]["subType"]["name"]!='Undefined'){
				var genre5 = document.createElement("li");
				genre5.appendChild(document.createTextNode(resp["classifications"][0]["subType"]["name"]));
				genre_content.appendChild(document.createTextNode(" | "));
				genre_content.appendChild(genre5);
				genre_count++;
			}
				
			if(genre_count > 0) {	
				genre_header.appendChild(document.createTextNode("Genres"));
				genre_div.appendChild(genre_header);
				genre_div.appendChild(genre_content);
				event_details_div.appendChild(genre_div);
			}

			if(resp['priceRanges']) {
				var prices_div = document.createElement("div");
				var prices_header = document.createElement("b");
				var prices_content = document.createElement("p");
				if(resp['priceRanges'][0]['min'] && resp['priceRanges'][0]['max']) {
					prices_content.appendChild(document.createTextNode(resp['priceRanges'][0]['min'] + ' - ' + resp['priceRanges'][0]['max'] + ' ' + resp['priceRanges'][0]['currency']));
				} else if(resp['priceRanges'][0]['min']) {
					prices_content.appendChild(document.createTextNode(resp['priceRanges'][0]['min'] + ' ' + resp['priceRanges'][0]['currency']));
				} else if(resp['priceRanges'][0]['max']) {
					prices_content.appendChild(document.createTextNode(resp['priceRanges'][0]['max'] + ' ' + resp['priceRanges'][0]['currency']));
				}
				prices_header.appendChild(document.createTextNode("Price Ranges"));
				prices_div.appendChild(prices_header);
				prices_div.appendChild(prices_content);
				event_details_div.appendChild(prices_div);
			}

			if(resp['dates']['status'] && resp['dates']['status']['code']) {
				var status_div = document.createElement("div");
				var status_header = document.createElement("b");
				var status_content = document.createElement("p");
				status_content.appendChild(document.createTextNode(resp['dates']['status']['code']));
				status_header.appendChild(document.createTextNode("Ticket Status"));
				status_div.appendChild(venue_header);
				status_div.appendChild(venue_content);
				event_details_div.appendChild(status_div);
			}

			var buy_div = document.createElement("div");
			var buy_header = document.createElement("b");
			var buy_content = document.createElement("a");
			buy_content.href = resp['url'];
			buy_content.setAttribute("target", "_blank");
			buy_content.appendChild(document.createTextNode("Ticketmaster"));
			buy_header.appendChild(document.createTextNode("Buy Ticket At: "));
			buy_div.appendChild(buy_header);
			buy_div.appendChild(document.createElement("br"));
			buy_div.appendChild(buy_content);
			event_details_div.appendChild(buy_div);

			table_div.appendChild(event_details_div);

			if(resp['seatmap'] && resp['seatmap']['staticUrl']) {
				var seatmap_div = document.createElement("div");
				seatmap_div.setAttribute("id", "seatmap_div");
				var seatmap_content = document.createElement("img");
				seatmap_content.src = resp['seatmap']['staticUrl'];
				seatmap_div.appendChild(seatmap_content);
				table_div.appendChild(seatmap_div);
			}
		}

		function venue_map_display(isHidden) {
			var dis_msg = document.getElementById("venue_map_display");
			var venue_details_table = document.getElementById("venue_details_table");
			var current_img = document.getElementById("venue_map_display_img");
			if(isHidden) {
				current_img.src = 'http://csci571.com/hw/hw6/images/arrow_up.png';
				dis_msg.innerHTML = 'click to hide venue info';
				venue_details_table.style.display="table";
				showMap(Number(venue_details_table.getAttribute("lati")), Number(venue_details_table.getAttribute("lang")), 'embedded_venue_map');
				current_img.onclick = function(){venue_map_display(false)};
				return;
			}
			current_img.src = 'http://csci571.com/hw/hw6/images/arrow_down.png';
			dis_msg.innerHTML = 'click to show venue info';
			venue_details_table.style.display="none";
			current_img.onclick = function(){venue_map_display(true)};
		}

		function venue_image_display(isHidden) {
			var dis_msg = document.getElementById("venue_img_display_node");
			var venue_details_table = document.getElementById("venue_img_container");
			var current_img = document.getElementById("venue_img_display_img");
			if(isHidden) {
				current_img.src = 'http://csci571.com/hw/hw6/images/arrow_up.png';
				dis_msg.innerHTML = 'click to hide venue info';
				venue_details_table.style.display="block";
				current_img.onclick = function(){venue_image_display(false)};
				return;
			}
			current_img.src = 'http://csci571.com/hw/hw6/images/arrow_down.png';
			dis_msg.innerHTML = 'click to show venue info';
			venue_details_table.style.display="none";
			current_img.onclick = function(){venue_image_display(true)};
		}

		function populateVenueDetails(resp) {
			var table_div = document.getElementById("venue_details_content");
			removeContent(table_div);

			var dis_msg = document.createElement("p");
			dis_msg.setAttribute("id", "venue_map_display");
			dis_msg.appendChild(document.createTextNode("Click To show venue info"));
			var img = document.createElement("img");
			img.setAttribute("id", "venue_map_display_img");
			img.src="http://csci571.com/hw/hw6/images/arrow_down.png";
			img.onclick = function(){venue_map_display(true)};

			table_div.appendChild(dis_msg);
			table_div.appendChild(img);

			var venue_details_table = document.createElement("table");
			venue_details_table.setAttribute("id", "venue_details_table");
			venue_details_table.setAttribute("lati", resp['_embedded']['venues'][0]['location']['latitude']);
			venue_details_table.setAttribute("lang", resp['_embedded']['venues'][0]['location']['longitude']);
			venue_details_table.style.display = "none";
			var row = document.createElement("tr");
			var col1 = document.createElement("td");
			var nametext = document.createTextNode("Name");
			col1.appendChild(nametext);
			var col2 = document.createElement("td");
			var col2_content;
			if(resp['_embedded'] && resp['_embedded']['venues'] && resp['_embedded']['venues'][0]['name']) {
				col2_content = document.createTextNode(resp['_embedded']['venues'][0]['name']);
			} else {
				col2_content = document.createTextNode("N/A");
			}
			col2.appendChild(col2_content);
			row.appendChild(col1);
			row.appendChild(col2);
			venue_details_table.appendChild(row);

			row = document.createElement("tr");
			col1 = document.createElement("td");
			nametext = document.createTextNode("Map");
			col1.appendChild(nametext);
			var parent_col2 = document.createElement("td");
			var super_col2 = document.createElement("div");
			super_col2.setAttribute("id", "venue_map_parent");
			col2 = document.createElement("div");
			var modes_container = document.createElement("ul");
			var mode1 = document.createElement("li");
			nametext = document.createTextNode("Walk There");
			mode1.appendChild(nametext);
			mode1.onclick = function() {initMap(Number(resp['_embedded']['venues'][0]['location']['latitude']), Number(resp['_embedded']['venues'][0]['location']['longitude']), 'embedded_venue_map', 'WALKING')};
			modes_container.appendChild(mode1);

			var mode2 = document.createElement("li");
			nametext = document.createTextNode("Bike There");
			mode2.appendChild(nametext);
			mode2.onclick = function(){initMap(Number(resp['_embedded']['venues'][0]['location']['latitude']), Number(resp['_embedded']['venues'][0]['location']['longitude']), 'embedded_venue_map', 'BICYCLING');}
			modes_container.appendChild(mode2);


			var mode3 = document.createElement("li");
			nametext = document.createTextNode("Drive There");
			mode3.appendChild(nametext);
			mode3.onclick = function(){initMap(Number(resp['_embedded']['venues'][0]['location']['latitude']), Number(resp['_embedded']['venues'][0]['location']['longitude']), 'embedded_venue_map', 'DRIVING')};
			modes_container.appendChild(mode3);

			super_col2.appendChild(modes_container);

			col2.setAttribute('id', 'embedded_venue_map');
			super_col2.appendChild(col2);
			parent_col2.appendChild(super_col2);
			row.appendChild(col1);
			row.appendChild(parent_col2);
			venue_details_table.appendChild(row);

			row = document.createElement("tr");
			col1 = document.createElement("td");
			nametext = document.createTextNode("Address");
			col1.appendChild(nametext);
			col2 = document.createElement("td");
			if(resp['_embedded']['venues'] && resp['_embedded']['venues'][0]['address'] && resp['_embedded']['venues'][0]['address']['line1']){
				col2_content = document.createTextNode(resp['_embedded']['venues'][0]['address']['line1']);
			} else {
				col2_content = document.createTextNode("N/A");
			}
			col2.appendChild(col2_content);
			row.appendChild(col1);
			row.appendChild(col2);
			venue_details_table.appendChild(row);

			row = document.createElement("tr");
			col1 = document.createElement("td");
			nametext = document.createTextNode("city");
			col1.appendChild(nametext);
			col2 = document.createElement("td");
			if(resp['_embedded']['venues'][0]['city']['name']) {
				col2_content = document.createTextNode(resp['_embedded']['venues'][0]['city']['name']);
				if(resp['_embedded']['venues'][0]['state']['statecode']) {
					col2_content += ', ' + document.createTextNode(resp['_embedded']['venues'][0]['state']['statecode']);
				}
			} else if(resp['_embedded']['venues'][0]['state']['statecode']) {
				col2_content = document.createTextNode(resp['_embedded']['venues'][0]['state']['statecode']);
			} else {
				col2_content = document.createTextNode("N/A");
			}
			col2.appendChild(col2_content);
			row.appendChild(col1);
			row.appendChild(col2);
			venue_details_table.appendChild(row);

			row = document.createElement("tr");
			col1 = document.createElement("td");
			nametext = document.createTextNode("Postal code");
			col1.appendChild(nametext);
			col2 = document.createElement("td");
			if(resp['_embedded']['venues'][0]['postalCode']) {
				col2_content = document.createTextNode(resp['_embedded']['venues'][0]['postalCode']);
			} else {
				col2_content = document.createTextNode("N/A");
			}
			col2.appendChild(col2_content);
			row.appendChild(col1);
			row.appendChild(col2);
			venue_details_table.appendChild(row);

			row = document.createElement("tr");
			col1 = document.createElement("td");
			nametext = document.createTextNode("Upcoming Events");
			col1.appendChild(nametext);
			col2 = document.createElement("td");
			var link = document.createElement("a");
			link.href = resp['_embedded']['venues'][0]['url'];
			link.setAttribute("target", "_blank");
			if(resp['_embedded']['venues'][0]['name']) {
				col2_content = document.createTextNode(resp['_embedded']['venues'][0]['name'] + ' Tickets');
			} else {
				col2_content = document.createTextNode("N/A");
			}
			link.appendChild(col2_content);
			col2.appendChild(link);
			row.appendChild(col1);
			row.appendChild(col2);
			venue_details_table.appendChild(row);

			table_div.appendChild(venue_details_table);

			var venue_img_container = document.createElement("div");
			venue_img_container.setAttribute("id", "venue_img_container");
			var venue_image = document.getElementById("venue_image");
			var venue_img_display_node = document.createElement("p");
			venue_img_display_node.setAttribute("id", "venue_img_display_node");
			venue_img_display_node.appendChild(document.createTextNode("Click To show venue photos"));
			var venue_img_node= document.createElement("img");
			venue_img_node.setAttribute("id", "venue_img_display_img");
			venue_img_node.src="http://csci571.com/hw/hw6/images/arrow_down.png";
			venue_img_node.onclick = function(){venue_image_display(true)};
			venue_image.appendChild(venue_img_display_node);
			venue_image.appendChild(venue_img_node);
			if(resp['_embedded']['venues'] && resp['_embedded']['venues'][0] && resp['_embedded']['venues'][0]['images']) {
				for (var img_count=0; img_count < resp['_embedded']['venues'][0]['images'].length; img_count++) {
					var image = document.createElement("img");
					image.src = resp['_embedded']['venues'][0]['images'][img_count]['url'];
					venue_img_container.appendChild(image);
				}
			} else {
				var para = document.createElement("p");
				para.setAttribute("id", "venue_image_no_record");
				para.appendChild(document.createTextNode("No Venue Photos Found"));
				venue_img_container.appendChild(para);
			}
			venue_img_container.style.display = "none";
			venue_image.appendChild(venue_img_container);
			showMap(Number(resp['_embedded']['venues'][0]['location']['latitude']), Number(resp['_embedded']['venues'][0]['location']['longitude']), 'embedded_venue_map');
		}

		function venue_details(loc) {
			var response;
			var xobj = new XMLHttpRequest();
	    	xobj.overrideMimeType("application/json");
	    	var url = "index.php?location="+loc;
	    	xobj.open('GET', url , false);
	    	xobj.onreadystatechange = function () {
	         if (xobj.readyState == 4 && xobj.status == "200") {
	            response = JSON.parse(xobj.responseText);
	            populateVenueDetails(response);
	          }
	    	};
	    	xobj.send(null);
		}

		function event_description() {
			var response;
			var xobj = new XMLHttpRequest();
	    	xobj.overrideMimeType("application/json");
	    	var url = "index.php?eventid="+this.getAttribute('eventid');
	    	xobj.open('GET', url , false);
	    	xobj.onreadystatechange = function () {
	         if (xobj.readyState == 4 && xobj.status == "200") {
	            response = JSON.parse(xobj.responseText);
	            populateEventDetails(response);
	          }
	    	};
	    	xobj.send(null); 
	    	venue_details(this.getAttribute('loc'));
		}

		function venue_description() {
			var ele = document.getElementById("google_maps_popup");
			if(ele.style.display=="block"){
				ele.style.display= "none";
				return;
			}
			var lati = this.getAttribute('lati');
			var lang = this.getAttribute('lang');
			showMapPositioned(this, Number(lati), Number(lang), 'google_maps_popup');
			setDirectionDiv(Number(lati), Number(lang), 'google_maps_popup');
			return;
		}

		function checkLocation(obj) {
			if(obj.value == 'here') {
				document.getElementById("custom_location").removeAttribute("required");
				document.getElementById("custom_location").disabled = true;
				document.getElementById("custom_location").value = "";
				getDetails();
				return;
			}
			document.getElementById("custom_location").setAttribute("required", "");
			document.getElementById("custom_location").disabled = false;

		}
	</script>

	<script type="text/javascript">
		function populateNearbyLocations(resp) {
			clearAll(false);
			var data_arr = res;
			var res = resp;
			var table_div = document.getElementById("content_section");
			var table = document.createElement("table");
			var header = new Array("Date", "Icon", "Event", "Genre", "Venue");
			var row = document.createElement("tr");
			for(var i=0; i<header.length; i++) {
				var th = document.createElement("th");
				th.appendChild(document.createTextNode(header[i]));
				row.appendChild(th);
			}
			table.appendChild(row);
			for(var i = 0 ; i < res.length; i++) {
				var row = document.createElement("tr");
				for(var j=0; j< header.length; j++){
					var col = document.createElement("td");
					if(j==0) {
						var content = document.createElement("p");
						content.appendChild(document.createTextNode(res[i]['dates']['start']['localDate']));
						content.appendChild(document.createElement("br"));
						if(res[i]['dates']['start']['localTime'] !==undefined) {
							content.appendChild(document.createTextNode(res[i]['dates']['start']['localTime']));
						}
						col.appendChild(content);

					}
					else if(j==1) {
						var img = document.createElement("img");
						var img_res = res[i]['images'];
						var src = img_res[0]['url'];
						var width = img_res[0]['width']+'px';
						var height = img_res[0]['height']+'px';
						for(var k = 0; k < img_res.length; k++) {
							if(img_res[k]['width'] == 100 || img_res[k]['height']==56) {
								src = img_res[k]['url'];
								width = '100px';
								height = '56px';
								break;
							}
						}
						img.src = src;
						col.appendChild(img);
					} else if (j==2) {
						var name = document.createElement("p");
						name.setAttribute("class", "event_name");
						name.setAttribute("eventId", res[i]['id']);
						name.setAttribute("class", "event_name");
						name.setAttribute("lati", res[i]['_embedded']['venues'][0]['location']['latitude']);
						name.setAttribute("lang", res[i]['_embedded']['venues'][0]['location']['longitude']);
						name.setAttribute("loc", res[i]['_embedded']['venues'][0]['name']);
						name.onclick = event_description;
						var text = document.createTextNode(res[i]['name']);
						name.appendChild(text)
						col.appendChild(name);
					} else if(j==3) {
						if(res[i]['classifications'] && res[i]['classifications'][0]['segment'] && res[i]['classifications'][0]['segment']['name']) {
							col.appendChild(document.createTextNode(res[i]['classifications'][0]['segment']['name']));
						} else {
							col.appendChild(document.createTextNode("N/A"));
						}
					} else {
						var venue = document.createElement("p");
						venue.setAttribute("class", "venue_name");
						venue.setAttribute("lang", res[i]['_embedded']['venues'][0]['location']['longitude']);
						venue.setAttribute("lati", res[i]['_embedded']['venues'][0]['location']['latitude']);
						venue.onclick = venue_description;
						var text = document.createTextNode(res[i]['_embedded']['venues'][0]['name']);
						venue.appendChild(text);
						col.appendChild(venue);
					}
					row.appendChild(col);
				}
				table.appendChild(row);
			}
			table_div.appendChild(table);
		}

	</script>
	<script type="text/javascript">
		function getGeoLocationDetails(url, callback) {
			var req = new XMLHttpRequest();
			req.open('GET',url,true);
			req.responseType = 'json';
			req.onload = function() {
				var status = req.status;
				if(status === 200) {
					document.getElementById("location_lat").value = req.response['lat'];
					document.getElementById("location_lon").value = req.response['lon'];
					document.getElementById("search_button").disabled = false;
					callback(null, req.response);
				} else {
					callback(status, req.response);
				}
			};
			req.send();
		};

		function getDetails(){
			getGeoLocationDetails('http://ip-api.com/json', 
				function(err, data){
					if (err !== null) {
					} else {
					}
				})
		}
	</script>
	<title>HW6</title>
</head>
<body onload="getDetails()">
	<div id="searchbox_container">
		<h1><i>Events Search</i></h1><hr>
		<form name="searchbox_form" id="searchbox" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
			<input type="hidden" id="location_lat" name="location_lat" value="">
			<input type="hidden" name="location_lon" id="location_lon" value="">
			<ul>
				<li><b>Keyword</b></li><input type="text" name="search_keyword" id="search_keyword" value="<?php echo isset($_POST['search_keyword']) ? $_POST['search_keyword'] : '' ?>" required>
				<li><b>Category</b></li>
				<select name="category_list" id="category_list">
					<option value="" <?php if (isset($_POST['category_list']) && $_POST['category_list']=="") echo "selected";?>>default</option>
					<option value="KZFzniwnSyZfZ7v7nJ" <?php if (isset($_POST['category_list']) && $_POST['category_list']=="KZFzniwnSyZfZ7v7nJ") echo "selected";?>>Music</option>
					<option value="KZFzniwnSyZfZ7v7nE" <?php if (isset($_POST['category_list']) && $_POST['category_list']=="KZFzniwnSyZfZ7v7nE") echo "selected";?>>Sports</option>
					<option value="KZFzniwnSyZfZ7v7na" <?php if (isset($_POST['category_list']) && $_POST['category_list']=="KZFzniwnSyZfZ7v7na") echo "selected";?>>Arts & Theatre</option>
					<option value="KZFzniwnSyZfZ7v7nn" <?php if (isset($_POST['category_list']) && $_POST['category_list']=="KZFzniwnSyZfZ7v7nn") echo "selected";?>>Film</option>
					<option value="KZFzniwnSyZfZ7v7n1" <?php if (isset($_POST['category_list']) && $_POST['category_list']=="KZFzniwnSyZfZ7v7n1") echo "selected";?>>Miscellaneous</option>
				</select>
				<li><b>Distance (miles)</b></li><input type="text" placeholder="distance" name="distance_in_miles" value="<?php echo isset($_POST['distance_in_miles']) ? $_POST['distance_in_miles'] : '10' ?>" id="distance_in_miles" required>
				<div id="from_location_div">
					<li><b>from</b></li>
					<input type="radio" name="from_location" value="here" id="here_loc" <?php if(isset($_POST['from_location']) && $_POST['from_location'] == 'here')  echo ' checked="checked"'; elseif(!isset($_POST['from_location']))  echo ' checked="checked"'; ?> onclick="checkLocation(this);"><p id="current_loc">Here</p><br>

					<input type="radio" name="from_location" value="custom_loc" id="new_loc" <?php if(isset($_POST['from_location']) && $_POST['from_location'] == 'custom_loc')  echo ' checked="checked"'; ?> onclick="checkLocation(this);">

					<input type="text" name="custom_location" placeholder="location" id="custom_location" value="<?php echo isset($_POST['custom_location']) ? $_POST['custom_location'] : '' ?>" disabled>
				</div>

				<input type="submit" name="search_for_location" value="search" id="search_button" disabled="true">
				<input type="reset" name="clear_" value="clear" id="clear_button" onclick="clearAll(true);">
			</ul>			
		</form>		
	</div>
	<div id="content_section"></div>
	<div id="venue_details_content"></div>
	<div id="venue_image"></div>
	<div id="google_maps_popup"></div>
	<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmOJZ9uWZmpdms5RC0OCVPhCQuWgLantQ">
    </script>
</body>
<script type="text/javascript">
	var form = document.getElementById("searchbox");
	form.addEventListener("submit", function(event)
	{
		event.preventDefault();
		clearAll(false);
	    var results;
	    var url = form.action;
	    var args = "";
    	var form_details = new FormData(form);
    	for(var key_val of form_details.entries()) {
    		args += key_val[0] + "=" + encodeURIComponent(key_val[1]) + "&";
    	}
    	var xmlhttp = new XMLHttpRequest();
		xmlhttp.open("POST", url, false);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(args);
		try {
			results = xmlhttp.responseText;
			var location = results.split("----")[0];
			results = JSON.parse(results.split("----")[1]);
			document.getElementById("location_lat").value = location.split(",")[0];
			document.getElementById("location_lon").value = location.split(",")[1];
			populateNearbyLocations(results);
		 } catch(err) {
			var main_content = document.getElementById("content_section");
			var no_records_msg = document.createElement("p");
			no_records_msg.setAttribute("id", "no_records");
			no_records_msg.appendChild(document.createTextNode("No Records has been found"));
			main_content.appendChild(no_records_msg);
		}
		return;
});
	
	</script>
	
</html>