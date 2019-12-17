<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | Sensor Array Overview</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>

		<?

		$sensor_array_uuid = substr($_GET[ 'url' ], strrpos($_GET[ 'url' ], '/') + 1);

		?>

		<div class="grid-item"><div>
			<h1>Map</h1>

			<div id="map" style="height: 512px"></div>

			<script>
				var map;
				function initMap() {
					<? 

					$sensor_array = SensorArrayManager::get_sensor_arrays()[ $sensor_array_uuid ];

					echo "map = new google.maps.Map(document.getElementById('map'), {
						center: {lat: " . $sensor_array[ 'latitude' ] . ", lng: " . $sensor_array[ 'longitude' ] . "},
						zoom: 15
					});";

					echo "new google.maps.Marker({
					position: {
						lat: " . $sensor_array[ 'latitude' ] . ", 
						lng: " . $sensor_array[ 'longitude' ] . "}, 
						map: map, 
						title: '" . $sensor_array[ 'name' ] . "', 
						icon: '../static/img/maps_sensor_icon.png'
					});";

					?>
				}
			</script>

			<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCr9YlPj0UvSkEpK6GIaA4JqFZmWwujrg4&callback=initMap"
			async defer></script>
		</div></div>

		<div class="grid-item"><div>
			<h1>Sensors <span style="color: grey; font-size: small"><? echo SensorManager::get_count( $sensor_array_uuid ); ?> total</span></h1>
			<? SensorManager::print_sensor_list( false, $sensor_array_uuid ); ?>
		</div></div>

		<div class="grid-item"><? include 'elements/panels/eventsLog.php' ?></div>
	</div>
</body>
</html>