<?

require_once( 'classes/sensorArrayManager.php' );
SensorArrayManager::create_sensor_array( $_POST["name"], $_POST["description"], $_POST["longitude"], $_POST["latitude"], $_POST["system"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>