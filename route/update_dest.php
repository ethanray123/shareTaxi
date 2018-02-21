<?php
  require('../connect.php');

	?>
<!DOCTYPE html>
<html lang="en">
	<head>
	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta charset="utf-8">
	<title>Create Route | ShareTaxi</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/general_style.css">
	<style>
		#map {
			height: 300px;
		}
	</style>
</head>
<body>
<div class="container">
<!-- Content -->
<div class="row">
	<div class="col-xs-12">
	<!-- Actual Form -->
		<form method="POST" action="update.php">
			<div class="form-group">
				<label for="user">Destination</label>
				<input type="text" name="destination" class="form-control" id="destination" placeholder="Search Box" required="">
			</div>
			<input type="hidden" name="destLat" class="form-control" id="destLat" alt="10.3541">
			<input type="hidden" name="destLong" class="form-control" id="destLong" alt=" 123.9116">
			<input type="hidden" name="source" class="form-control" id="source">
			<input type="hidden" name="srcLat" class="form-control" id="srcLat">
			<input type="hidden" name="srcLong" class="form-control" id="srcLong">
			<input type="hidden" name= "ID" class= "form-countrol" id="ID" value="<?php echo $_SESSION['id'] ;?>">
			<div id="map"></div>
			<button type="submit" class="btn btn-success">Submit</button>
		</form>
	<!-- End Form -->
	</div><!-- .col-xs-12 -->
</div><!-- .row -->
</div><!-- .container -->
<script src="js/jquery.min.js"></script>
<script >
	// Note: This requires that you consent to location sharing when
	// prompted by your browser. If you see the error "The Geolocation service
	// failed.", it means you probably did not give permission for the browser to
	// locate you.
	var map, infoWindow, geocoder, searchBox;
	var srcLat= <?php
		$result = mysqli_query($db, "SELECT * FROM pool WHERE user_id = {$_SESSION['id']} LIMIT 1");
		$row = mysqli_fetch_assoc($result);

		$routeId = $row['route_id'];

		$result = mysqli_query($db, "SELECT * from route WHERE route_id = {$routeId} LIMIT 1");
		$row = mysqli_fetch_assoc($result);

		echo $row['destination_latitude'];
	?>;
	var srcLong= <?php
		echo $row['destination_longitude'];
	?>;
	// Previous Data
	prepareSourceDataForTransmission()
	// Initializing the map
	function initMap() {
		map = new google.maps.Map(document.getElementById('map'), {
		  center: {lat: srcLat  , lng: srcLong}, // Default USC Talamban Campus
		  zoom: 18
		});

		infoWindow = new google.maps.InfoWindow;
		geocoder = new google.maps.Geocoder;
		searchBox = new google.maps.places.SearchBox(document.getElementById('destination'));
		map.controls.push(document.getElementById('destination'));

		var markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
          var places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
		  }

          // Clear out the old markers.
          markers.forEach(function(marker) {
            marker.setMap(null);
          });
          markers = [];

          // For each place, get the icon, name and location.
          var bounds = new google.maps.LatLngBounds();
          places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }
            // Create a marker for each place.
			var pos = {
			  lat: place.geometry.location.lat(),
			  lng: place.geometry.location.lng()
			};
			// For db
			$('#destLat').val(pos.lat);
			$('#destLong').val(pos.lng);

            markers.push(new google.maps.Marker({
              map: map,
              title: place.name,
              position: pos
            }));

            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds);
        });
	}

	// If user clicks "Your current location"
	// Google takes his latlong and updates the map
	// Also places the address inside the input box

	function handleLocationError(browserHasGeolocation, infoWindow, pos) {
		infoWindow.setPosition(pos);
		infoWindow.setContent(browserHasGeolocation ?
						  'Error: The Geolocation service failed.' :
						  'Error: Your browser doesn\'t support geolocation.');
		infoWindow.open(map);
	}

	function prepareSourceDataForTransmission(){
		var srcLat	= "<?php echo $_POST['srcLat'] ?>",
			srcLong = "<?php echo $_POST['srcLong'] ?>",
			source	= "<?php echo $_POST['source'] ?>"; // actually useless, but might be needed later

		$('#srcLat').val(srcLat);
		$('#srcLong').val(srcLong);
		$('#source').val(source);
	}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBOsw4rpr5IU_mQEmRbiz1EMA3YCtpPaw&callback=initMap&libraries=places"></script>
</body></html>
