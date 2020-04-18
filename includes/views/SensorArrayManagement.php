<?

$system_center = SystemManager::get_system_center();

?>
<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | Array Management</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
	<script>
		var map, marker;

		function initMap() {
			map = new google.maps.Map(document.getElementById('map'), {
				center: {lat: <? echo $system_center[ 'latitude' ]; ?>, lng: <? echo $system_center[ 'longitude' ]; ?>},
				zoom: 12
			});

			map.setOptions({draggableCursor:'crosshair'});

			google.maps.event.addListener( map, 'click', function( event ) {
				if ( !marker ) {
					marker = new google.maps.Marker({position: {lat: 0, lng: 0}, map: map, title: 'Sensor Array', icon: '../static/img/maps_sensor_icon.png'});
				}

				marker.setPosition( event.latLng );

				$( "input[name='latitude']" ).val( event.latLng.lat() );
				$( "input[name='longitude']" ).val( event.latLng.lng() );
			});
		}
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCr9YlPj0UvSkEpK6GIaA4JqFZmWwujrg4&callback=initMap" async defer></script>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>
		<? include 'elements/prompt.php'; ?>

		<div class="grid-item"><div>
			<h1>Sensor Arrays <span class="sml-grey"><? echo SensorArrayManager::get_count(); ?> Total</span></h1>
			<div class="overflow-list-512"><? SensorArrayManager::print_sensor_array_list( true ); ?></div>
		</div></div>

		<div class="grid-item"><div>
			<h1>Add New Sensor Array</h1>
			<form action="../includes/services/sensorArrayCreate.php" method="post">
				<div style="display: inline-block; margin-right: 16px"><p>System</p><? SystemManager::create_system_dropdown(); ?><br><br></div>
				<div style="display: inline-block; margin-right: 16px"><p>Name</p><input type="text" name="name"><br><br></div>
				<div style="display: inline-block"><p>Description</p><input type="text" name="description"><br><br></div>
				<div id="map"></div>
				<input type="text" name="latitude" hidden>
				<input type="text" name="longitude" hidden><br>
				<input type="submit" value="Create Sensor Array">
			</form>
		</div></div>

		<div class="grid-item"><div>
			<h1>Import From HydroVu</h1>

			<form action="../admin/hydrovu-import" method="post" enctype="multipart/form-data">
				<div><p>Client ID</p><input type="text" name="client_id"><br><br></div>
				<div><p>Client Secret</p><input type="text" name="client_secret"><br><br></div>
				<input type="submit" value="Import">
			</form>
		</div></div>

		<div class="grid-item"><div>
			<h1>Import From CSV</h1>

			<form action="../admin/array-import" method="post" enctype="multipart/form-data">
				<div style="display: inline-block"><p>Sensor Array</p><? SensorArrayManager::create_sensor_array_dropdown(); ?></div>
				<br><br>
				<input type="file" name="CSVfile" id="CSVfile"><br><br>
				<input type="submit" value="Import Sensor Array Sensors">
			</form>
		</div></div>

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>