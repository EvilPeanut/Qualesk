<?

require_once( 'classes/sensorManager.php' );
SensorManager::create_sensor_type( $_POST["name"], $_POST["description"], $_POST["data_type"], $_POST["data_unit"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>