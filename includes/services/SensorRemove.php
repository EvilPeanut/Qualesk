<?

require_once( 'classes/sensorManager.php' );
SensorManager::remove_sensor( $_GET["uuid"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>