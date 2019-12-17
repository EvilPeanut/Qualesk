<?

require_once( 'classes/sensorArrayManager.php' );
SensorArrayManager::remove_sensor_array( $_GET["uuid"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>