<?

class ElementHelper
{

	public static function get_uuids_from_sensor( $sensor_uuid ) {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT sensor_array_uuid, sensor_type, system_uuid FROM sensors WHERE uuid='$sensor_uuid' INNER JOIN sensor_arrays ON sensors.sensor_array_uuid = sensor_arrays.uuid" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $sensor_array_uuid, $sensor_type, $system_uuid );
			$statement->fetch();

			$sensor = array();
			$sensor[ 'system_uuid'] = $system_uuid;
			$sensor[ 'sensor_array_uuid' ] = $sensor_array_uuid;
			$sensor[ 'sensor_uuid' ] = $sensor_uuid;
			$sensor[ 'sensor_type_uuid' ] = $sensor_type;

		}

		return $sensor;
	}

}

?>