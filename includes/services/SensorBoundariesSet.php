<?

require_once( 'classes/sensorManager.php' );

$url = parse_url( $_SERVER[ 'HTTP_REFERER' ] );
$sensor_uuid = substr( $url[ 'path' ], strrpos( $url[ 'path' ], '/' ) + 1 );

SensorManager::set_reading_boundaries( $sensor_uuid, $_POST["upper_urgent_boundary"], $_POST["upper_warning_boundary"], $_POST["lower_warning_boundary"], $_POST["lower_urgent_boundary"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>