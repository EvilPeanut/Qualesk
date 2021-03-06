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
		<? include 'elements/prompt.php'; ?>

		<?

		$sensor_array_uuid = substr($_GET[ 'url' ], strrpos($_GET[ 'url' ], '/') + 1);

		?>

		<!-- Add prompt -->
		<div id="div_overlay_add" class="overlay">
			<div id="div_prompt">
				<h1>Add Compound Graph</h1>

				<form id="form_create" action="../includes/services/graphCreate.php" method="post">
					<input type="hidden" name="sensor_array_uuid" value="<? echo $sensor_array_uuid ?>">
					<p>Name</p><input type="text" name="name" style="width: 256px"><br><br>
					<p>Description</p><input type="text" name="description" style="width: 256px"><br><br>
					<div onclick="$( '#form_create' ).submit()" class="inline-button"><p>Create</p></div>
					<div onclick="$( '#div_overlay_add' ).hide()" class="inline-button"><p>Close</p></div>
				</form>
			</div>
		</div>
		<!-- Add prompt -->

		<div class="grid-item">
			<div id="map"></div>

			<script>
				var map;
				function initMap() {
					<? 

					$sensor_array = SensorArrayManager::get_sensor_arrays()[ $sensor_array_uuid ];

					echo "map = new google.maps.Map(document.getElementById('map'), {
						center: {lat: " . $sensor_array[ 'latitude' ] . ", lng: " . $sensor_array[ 'longitude' ] . "},
						zoom: 15
					});";

					echo 'new google.maps.Marker({
					position: {
						lat: ' . $sensor_array[ 'latitude' ] . ', 
						lng: ' . $sensor_array[ 'longitude' ] . '}, 
						map: map, 
						title: "' . $sensor_array[ 'name' ] . '", 
						icon: "../static/img/maps_sensor_icon.png"
					});';

					?>
				}
			</script>

			<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCr9YlPj0UvSkEpK6GIaA4JqFZmWwujrg4&callback=initMap"
			async defer></script>
		</div>

		<div class="grid-item"><div>
			<h1>Sensors <span class="sml-grey"><? echo SensorManager::get_count( $sensor_array_uuid ); ?> Total</span></h1>
			<? SensorManager::print_sensor_list( false, $sensor_array_uuid ); ?>

			<br>

			<h1 class="graph-title">Compound Graphs <span class="sml-grey"><? echo GraphManager::get_count( $sensor_array_uuid ); ?> Total</span></h1>

			<img class="icon" src="../static/img/icon_add.png" onclick="$( '#div_overlay_add' ).show()"/>
			
			<div style="max-height: 512px; margin-top: 16px; overflow-y: auto">
				<? GraphManager::print_graph_list( true, $sensor_array_uuid ); ?>
			</div>
		</div></div>

		<div class="grid-item"><? include 'elements/panels/eventsLog.php' ?></div>

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>