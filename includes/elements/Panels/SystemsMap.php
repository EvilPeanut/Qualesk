<div class="grid-item grid-item-2x1">
	<div id="map"></div>

	<?

	$system_center = SystemManager::get_system_center();

	?>

	<script>
		var map;
		function initMap() {
			map = new google.maps.Map(document.getElementById('map'), {
				center: {lat: <? echo $system_center[ 'latitude' ]; ?>, lng: <? echo $system_center[ 'longitude' ]; ?>},
				zoom: 12
			});

			var markers = [], info_windows = [];

			//
			<? 

			$sensor_arrays = SensorArrayManager::get_sensor_arrays();

			foreach ( $sensor_arrays as $uuid => $sensor_array ) {
				echo '
				markers["' . $uuid . '"] = new google.maps.Marker({position: {lat: ' . $sensor_array[ "latitude" ] . ', lng: ' . $sensor_array[ "longitude" ] . '}, map: map, title: "' . $sensor_array[ "name" ] . '", icon: "../static/img/maps_sensor_icon.png"});

				info_windows["' . $uuid . '"] = new google.maps.InfoWindow({
					content: "<a href=\"../array/' . $uuid . '\"><h1>' . $sensor_array[ "name" ] . '</h1></a><p>' . $sensor_array[ "description" ] . '</p>"
				});

				markers["' . $uuid . '"].addListener("click", function() {
					info_windows["' . $uuid . '"].open(map, markers["' . $uuid . '"]);
				});
				';
			}

			?>

			// Automatically zoom map to display all markers
			if ( Object.keys( markers ).length > 1 ) {
				var bounds = new google.maps.LatLngBounds();

				for ( var key in markers ) {
					bounds.extend( markers[ key ].position );
				}

				map.fitBounds( bounds, 50 );

				// Move the map south by 29 pixels to account for pin height
				map.panBy( 0, -29 );
			}
		}
	</script>

	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCr9YlPj0UvSkEpK6GIaA4JqFZmWwujrg4&callback=initMap"
	async defer></script>
</div>