<?

class LogManager
{

	public static function print_log( $system_uuid = NULL, $sensor_array_uuid = NULL ) {
		require( 'services/DatabaseConnect.php' );

		$where_clause = "";

		if ( $system_uuid != NULL ) {
			$where_clause = "WHERE system_uuid='$system_uuid'";
		}

		if ( $sensor_array_uuid != NULL ) {
			$where_clause = "WHERE sensor_array_uuid='$sensor_array_uuid'";
		}

		if ( $statement = $mysqli->prepare( "SELECT sensor_uuid, date, type, message FROM logs INNER JOIN sensors ON sensors.uuid = logs.sensor_uuid INNER JOIN sensor_arrays ON sensors.sensor_array_uuid = sensor_arrays.uuid $where_clause ORDER BY date DESC LIMIT 25" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $sensor_uuid, $date, $type, $message );
			
			while ( $statement->fetch() ) {
				if ( $type == "1" ) {
					$icon = "warning";
					$style = "background-color: rgba(255, 127, 0, 0.2)";
				} else if ( $type == "2" ) {
					$icon = "urgent";
					$style = "background-color: rgba(255, 0, 0, 0.2)";
				} else {
					$icon = "info";
					$style = "";
				}

				echo "<div style='$style' severity=$type><img style='padding: 4px 0px 0px 4px' src='../static/img/$icon.png'><p style='display: inline'><span style='padding: 0px 8px'>$date</span><a href='../sensor/$sensor_uuid'>$message</a></p></div>";
			}
		}
	}

}

?>