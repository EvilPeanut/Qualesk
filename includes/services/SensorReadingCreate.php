<?

require_once( 'classes/sensorManager.php' );

$url = parse_url( $_SERVER[ 'HTTP_REFERER' ] );
$sensor_uuid = substr( $url[ 'path' ], strrpos( $url[ 'path' ], '/' ) + 1 );

SensorManager::create_sensor_reading( $sensor_uuid, $_POST["date"], $_POST["data"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>