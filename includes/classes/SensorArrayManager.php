<?

require_once( 'services/uuid.php' );

class SensorArrayManager
{

	public static function create_sensor_array( $name, $description, $longitude, $latitude, $system ) {
		require_once( 'services/DatabaseConnect.php' );

		// Generate sensor array uuid
		$sensor_array_uuid = guidv4();

		// Insert in to database and create table
		if ( $statement = $mysqli->prepare( "INSERT INTO sensor_arrays (uuid, system_uuid, name, description, longitude, latitude) VALUES (?, ?, ?, ?, ?, ?)" ) ) {
			$statement->bind_param( 'ssssss', $sensor_array_uuid, $system, $name, $description, $longitude, $latitude );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}
	}

	public static function remove_sensor_array( $uuid ) {
		require_once( 'services/DatabaseConnect.php' );

		// Remove database records
		if ( $statement = $mysqli->prepare( "DELETE FROM sensor_arrays WHERE uuid=?" ) ) {
			$statement->bind_param( 's', $uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}

		if ( $statement = $mysqli->prepare( "SELECT uuid, sensor_type FROM sensors WHERE sensor_array_uuid=?;" ) ) {
			$statement->bind_param( 's', $uuid );
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $sensor_uuid, $sensor_type_uuid );
			
			$sensors = array();

			while ( $statement->fetch() ) {
				$sensors[ $sensor_uuid ] = $sensor_type_uuid;
			}
		}

		if ( $statement = $mysqli->prepare( "DELETE FROM sensors WHERE sensor_array_uuid=?" ) ) {
			$statement->bind_param( 's', $uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}

		foreach ( $sensors as $sensor_uuid => $sensor_type_uuid ) {
			if ( $statement = $mysqli->prepare( "DELETE FROM `sensor_$sensor_type_uuid` WHERE sensor_uuid=?" ) ) {
				$statement->bind_param( 's', $sensor_uuid );
				if ( !$statement->execute() ) {
					// TODO: Handle SQL error
				}
			}
		}
	}

	public static function get_sensor_arrays() {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT uuid, system_uuid, name, description, longitude, latitude FROM sensor_arrays;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $uuid, $system_uuid, $name, $description, $longitude, $latitude );
			
			$sensor_arrays = array();

			while ( $statement->fetch() ) {
				$sensor_arrays[ $uuid ] = array();
				$sensor_arrays[ $uuid ][ 'system_uuid' ] = $system_uuid;
				$sensor_arrays[ $uuid ][ 'name' ] = $name;
				$sensor_arrays[ $uuid ][ 'description' ] = $description;
				$sensor_arrays[ $uuid ][ 'longitude' ] = $longitude;
				$sensor_arrays[ $uuid ][ 'latitude' ] = $latitude;
			}

			return $sensor_arrays;
		}
	}

	public static function get_sensor_array( $sensor_array_uuid ) {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT uuid, system_uuid, name, description, longitude, latitude FROM sensor_arrays WHERE uuid=?;" ) ) {
			$statement->bind_param( 's', $sensor_array_uuid );
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $uuid, $system_uuid, $name, $description, $longitude, $latitude );
			
			$statement->fetch();

			$sensor_array = array();
			$sensor_array[ 'system_uuid' ] = $system_uuid;
			$sensor_array[ 'name' ] = $name;
			$sensor_array[ 'description' ] = $description;
			$sensor_array[ 'longitude' ] = $longitude;
			$sensor_array[ 'latitude' ] = $latitude;

			return $sensor_array;
		}
	}

	public static function get_count( $system_uuid = NULL ) {
		require( 'services/DatabaseConnect.php' );

		$where_clause = "";

		if ( $system_uuid != NULL ) {
			$where_clause = " WHERE system_uuid='$system_uuid'";
		}

		if ( $statement = $mysqli->prepare( "SELECT COUNT(*) FROM sensor_arrays $where_clause;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $count );

			$statement->fetch();

			return $count;
		}
	}

	public static function print_sensor_array_list( $allow_management = false, $system_uuid = NULL ) {
		require( 'services/DatabaseConnect.php' );

		$where_clause = "";

		if ( $system_uuid != NULL ) {
			$where_clause = " WHERE system_uuid='$system_uuid'";
		}

		$previous_system_uuid = NULL;
		$system = NULL;

		if ( $statement = $mysqli->prepare( "SELECT uuid, name, description, system_uuid FROM sensor_arrays $where_clause ORDER BY system_uuid DESC;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $uuid, $name, $description, $system_uuid );
			
			while ( $statement->fetch() ) {
				if ( $allow_management && $system_uuid != $previous_system_uuid ) {
					$system = SystemManager::get_system( $system_uuid );

					if ( $previous_system_uuid != NULL ) {
						echo "<br>";
					}
					
					echo "<p>" . ( $system[ 'name' ] == null ? "Unknown System" : $system[ 'name' ] ) . "</p>";

					$previous_system_uuid = $system_uuid;
				}

				if ( $allow_management ) {
					if ( strlen( $description ) > 0 ) {
						echo "<img src='../static/img/remove.png' style='cursor: pointer' onclick='show_prompt(\"Remove sensor array\", \"Are you sure you want to remove this sensor array?<br><br>Sensors under this sensor array will also be removed\", \"../includes/services/sensorArrayRemove.php?uuid=$uuid\")'><a href='../array/$uuid'><p style='display: inline'> $name <span style='color: grey'> - $description</span></p></a><br>";
					} else {
						echo "<img src='../static/img/remove.png' style='cursor: pointer' onclick='show_prompt(\"Remove sensor array\", \"Are you sure you want to remove this sensor array?<br><br>Sensors under this sensor array will also be removed\", \"../includes/services/sensorArrayRemove.php?uuid=$uuid\")'><a href='../array/$uuid'><p style='display: inline'> $name</p></a><br>";
					}
				} else {
					if ( strlen( $description ) > 0 ) {
						echo "<a href='../array/$uuid'><p>$name <span style='color: grey'> - $description</span></p></a><br>";
					} else {
						echo "<a href='../array/$uuid'><p>$name</p></a><br>";
					}
				}
			}
		}
	}

	public static function create_sensor_array_dropdown() {
		require( 'services/DatabaseConnect.php' );

		echo '<select name="sensor_array">';

		if ( $statement = $mysqli->prepare( "SELECT uuid, name FROM sensor_arrays;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $uuid, $name );
			
			while ( $statement->fetch() ) {
				echo "<option value='$uuid'>$name</option>";
			}
		}

		echo '</select>';
	}

}

?>