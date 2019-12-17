<?

require_once( 'classes/sensorManager.php' );
SensorManager::create_sensor( $_POST["name"], $_POST["description"], $_POST["sensor_type"], $_POST["sensor_array"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>