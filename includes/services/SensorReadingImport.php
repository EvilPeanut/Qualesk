<?

require_once( 'classes/sensorManager.php' );

$url = parse_url( $_SERVER[ 'HTTP_REFERER' ] );
$sensor_uuid = substr( $url[ 'path' ], strrpos( $url[ 'path' ], '/' ) + 1 );

// Read CSV
$row = 0;
$row_filter_id = 0;
if ( ( $handle = fopen( $_FILES[ "CSVfile" ][ "tmp_name" ], "r" ) ) !== FALSE ) {
	while ( ( $data = fgetcsv( $handle, 1000, "," ) ) !== FALSE ) {
		if ( $row == 0 ) {	        
			for ( $c = 0; $c < count( $data ); $c++ ) {
				if ( $row == 0 && $data[ $c ] == $_POST[ "sensor_name" ] ) {
					$row_filter_id = $c;
				}
			}
		} else if ( $row != 1 && strlen( $data[ $row_filter_id ] ) > 0 ) {
			SensorManager::create_sensor_reading( $sensor_uuid, $data[ 0 ], $data[ $row_filter_id ] );
		}

		$row++;
	}
	fclose($handle);
}

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>