<?

if ( !AccountManager::has_permission( 'admin_features' ) ) {
	header('Location: ../noperm');
}

?>
<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | Sensor Management</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>
		<? include 'elements/prompt.php'; ?>

		<div class="grid-item"><div>
			<h1>Sensors  <span style="color: grey; font-size: small"><? echo SensorManager::get_count(); ?> total</span></h1>
			<? SensorManager::print_sensor_list( true ); ?>
		</div></div>

		<div class="grid-item"><div>
			<h1>Add New Sensor</h1>
			<form action="../includes/services/sensorCreate.php" method="post">
				<p>Name</p><input type="text" name="name"><br><br>
				<p>Description</p><input type="text" name="description"><br><br>
				<p>Type</p><? SensorManager::create_sensor_type_dropdown() ?><br><br>
				<p>Sensor Array</p><? SensorArrayManager::create_sensor_array_dropdown() ?><br><br>
				<input type="submit" value="Create Sensor">
			</form>
		</div></div>

		<div class="grid-item"><div>
			<h1>Add New Sensor Type</h1>
			<form action="../includes/services/sensorTypeCreate.php" method="post">
				<p>Name</p><input type="text" name="name"><br><br>
				<p>Description</p><input type="text" name="description"><br><br>
				<input type="text" name="data_type" value="DOUBLE" hidden>
				<p>Unit</p><input type="text" name="data_unit"><br><br>
				<input type="submit" value="Create Sensor Type">
			</form>
		</div></div>
	</div>
</body>
</html>