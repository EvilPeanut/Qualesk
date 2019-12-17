<?

// Prevent timeout
set_time_limit( 0 );

require_once( 'classes/sensorManager.php' );

// Read CSV
$rows = array();
$sensor_type_uuids = array();
$sensor_uuids = array();

// CSV to array
if ( ( $handle = fopen( sys_get_temp_dir() . $_GET['file'], "r" ) ) !== FALSE ) {
	while ( ( $data = fgetcsv( $handle, 1000, "," ) ) !== FALSE ) {
		array_push( $rows, $data );
	}
	fclose($handle);
}

// Create sensors and sensor types
$i = 0;
$sensor_total = count( $rows[0] ) - 1;
foreach ( $rows[0] as $row ) {
	if ( $i != 0 ) {
		array_push( $sensor_type_uuids, SensorManager::create_sensor_type( $row, "", "DOUBLE", $rows[ 1 ][ $i ] ) );
		array_push( $sensor_uuids, SensorManager::create_sensor( $row, "", $sensor_type_uuids[ $i - 1 ], $_GET[ "sensor_array" ] ) );

		echo json_encode(array('stage' => 'sensor_create', 'total' => $sensor_total, 'current' => $i));
		flush();
		ob_flush();
	}
	$i++;
}

// Create sensor readings
$column_total = count( $rows[0] ) - 1;

for ( $column = 1; $column <= $column_total; $column++ ) { 
	$data_array = [];

	$row_index = 0;

	foreach ( $rows as $row ) {
		if ( $row_index > 1 ) {
			if ( strlen( $row[ $column ] ) > 0 ) {
				$reading = [ $row[ 0 ], $row[ $column ] ];
				array_push( $data_array, $reading );
			}
		}

		$row_index++;
	}

	SensorManager::create_multiple_sensor_reading( $sensor_uuids[ $column - 1 ], $data_array );

	echo json_encode(array('stage' => 'data_create', 'total' => ($column_total - 1), 'current' => $column));
	flush();
	ob_flush();	
} 

?>