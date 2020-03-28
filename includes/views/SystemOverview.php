<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | System Overview</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>

		<?

		$system_uuid = substr($_GET[ 'url' ], strrpos($_GET[ 'url' ], '/') + 1);
		$system_center = SystemManager::get_system_center( $system_uuid );

		?>

		<div class="grid-item"><div>
			<h1>Map</h1>

			<div id="map"></div>

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
						if ( $sensor_array[ 'system_uuid' ] == $system_uuid ) {
							echo "
							markers['$uuid'] = new google.maps.Marker({position: {lat: " . $sensor_array[ 'latitude' ] . ", lng: " . $sensor_array[ 'longitude' ] . "}, map: map, title: '" . $sensor_array[ 'name' ] . "', icon: '../static/img/maps_sensor_icon.png'});

							info_windows['$uuid'] = new google.maps.InfoWindow({
								content: '<a href=\'../array/$uuid\'><h1>" . $sensor_array[ 'name' ] . "</h1></a><p>" . $sensor_array[ 'description' ] . "</p>'
							});

							markers['$uuid'].addListener('click', function() {
								info_windows['$uuid'].open(map, markers['$uuid']);
							});
							";
						}
					}

					?>

					// Automatically zoom map to display all markers
					if ( Object.keys( markers ).length > 1 ) {
						var bounds = new google.maps.LatLngBounds();

						for ( var key in markers ) {
							bounds.extend( markers[ key ].position );
						}

						map.fitBounds( bounds, 50 );
					}
				}
			</script>

			<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCr9YlPj0UvSkEpK6GIaA4JqFZmWwujrg4&callback=initMap"
			async defer></script>
		</div></div>

		<div class="grid-item"><div>
			<h1>Sensor Arrays  <span class="sml-grey"><? echo SensorArrayManager::get_count( $system_uuid ); ?> Total</span></h1>
			<? SensorArrayManager::print_sensor_array_list( false, $system_uuid ); ?>
		</div></div>

		<div class="grid-item"><? include 'elements/panels/eventsLog.php' ?></div>

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>