<?

// Flush content to allow us to send data for AJAX
ob_implicit_flush(true);
ob_end_flush();

echo 'Loading';

// Includes
include 'services/hydroVu.php';

require_once( 'classes/systemManager.php' );
require_once( 'classes/sensorArrayManager.php' );
require_once( 'classes/sensorManager.php' );

$hydroVu = new HydroVu();

//
// Disable execution time out and authenticate
//
set_time_limit( 0 );

echo 'Authenticating';

$hydroVu->authorize( $_GET['client_id'], $_GET['client_secret'] );

//
// Friendly names system
//
echo 'Retrieving parameter and unit data';

$friendly_names = $hydroVu->get_friendly_names();

$parameter_names = [];
$unit_names = [];

foreach ($friendly_names['parameters'] as $key => $value) {
	$parameter_names[ strval( $key ) ] = strval( $value );
}

foreach ($friendly_names['units'] as $key => $value) {
	$unit_names[ strval( $key ) ] = strval( $value );
}

function getParameterName( $parameter_names, $param_id ) {
	if ( array_key_exists( $param_id, $parameter_names ) ) {
		return $parameter_names[ $param_id ];
	} else {
		return $param_id;
	}
}

function getUnitName( $unit_names, $unit_id ) {
	if ( array_key_exists( $unit_id, $unit_names ) ) {
		return $unit_names[ $unit_id ];
	} else {
		return $unit_id;
	}
}

//
// Create HydroVu system
//
echo 'Creating system';

$system_uuid = SystemManager::create_system( 'HydroVu', 'Imported HydroVu data' );

//
// Get locations and create sensor arrays
//
echo 'Retrieving location data';

$locations = $hydroVu->get_locations();

$location_uuids = [];

foreach ($locations as $location) {
	$location_uuids[$location['id']] = SensorArrayManager::create_sensor_array( $location['name'], $location['description'], $location['gps']['longitude'], $location['gps']['latitude'], $system_uuid );
}

//
// Get readings and create sensors
//
$reading_count = 0;

foreach ($locations as $location) {
	echo 'Retrieving readings for location ' . $location['name'];

	$sensor_readings = $hydroVu->get_readings($location['id'], $location['name']);
	foreach ($sensor_readings as $sensor_name => $sensor) {
		$sensor_type_uuid = SensorManager::create_sensor_type( $sensor_name, '', 'DOUBLE', getUnitName( $unit_names, $sensor['unitId'] ) );
		$sensor_uuid = SensorManager::create_sensor( getParameterName( $parameter_names, $sensor_name ), '', $sensor_type_uuid, $location_uuids[$location['id']] );

		$data_array = [];

		foreach ($sensor['readings'] as $reading) {
			array_push( $data_array, [gmdate( 'Y-m-d H:i:s', $reading['timestamp'] ), $reading['value']]);
			$reading_count += 1;
		}

		SensorManager::create_multiple_sensor_reading( $sensor_uuid, $data_array );
	}
}

?>