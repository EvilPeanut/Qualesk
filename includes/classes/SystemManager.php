<?

require_once( 'services/uuid.php' );

class SystemManager
{

	public static function create_system( $name, $description ) {
		require_once( 'services/DatabaseConnect.php' );

		// Generate system uuid
		$system_uuid = guidv4();

		// Insert in to database and create table
		if ( $statement = $mysqli->prepare( "INSERT INTO systems (uuid, name, description) VALUES (?, ?, ?)" ) ) {
			$statement->bind_param( 'sss', $system_uuid, $name, $description );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}
	}

	public static function remove_system( $uuid ) {
		require_once( 'services/DatabaseConnect.php' );

		// Remove database records
		if ( $statement = $mysqli->prepare( "DELETE FROM systems WHERE uuid=?" ) ) {
			$statement->bind_param( 's', $uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}

		if ( $statement = $mysqli->prepare( "SELECT uuid FROM sensor_arrays WHERE system_uuid=?;" ) ) {
			$statement->bind_param( 's', $uuid );
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $sensor_array_uuid );
			
			$sensor_arrays = array();

			while ( $statement->fetch() ) {
				array_push( $sensor_arrays, $sensor_array_uuid );
			}
		}

		if ( $statement = $mysqli->prepare( "DELETE FROM sensor_arrays WHERE system_uuid=?" ) ) {
			$statement->bind_param( 's', $uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}

		foreach ( $sensor_arrays as $sensor_array_uuid ) {
			if ( $statement = $mysqli->prepare( "SELECT uuid, sensor_type FROM sensors WHERE sensor_array_uuid=?;" ) ) {
				$statement->bind_param( 's', $sensor_array_uuid );
				$statement->execute();
				$statement->store_result();
				$statement->bind_result( $sensor_uuid, $sensor_type_uuid );
				
				$sensors = array();

				while ( $statement->fetch() ) {
					$sensors[ $sensor_uuid ] = $sensor_type_uuid;
				}
			}

			if ( $statement = $mysqli->prepare( "DELETE FROM sensors WHERE sensor_array_uuid=?" ) ) {
				$statement->bind_param( 's', $sensor_array_uuid );
				if ( !$statement->execute() ) {
					// TODO: Handle SQL error
				}
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

	public static function get_system_center( $system_uuid = NULL ) {
		$sensor_arrays = SensorArrayManager::get_sensor_arrays();

		$sensor_array_count = 0;
		$latitude_total = 0;
		$longitude_total = 0;

		foreach ( $sensor_arrays as $uuid => $sensor_array ) {
			if ( $system_uuid == NULL || $sensor_array[ 'system_uuid' ] == $system_uuid ) {
				$sensor_array_count++;
				$latitude_total += $sensor_array[ 'latitude' ];
				$longitude_total += $sensor_array[ 'longitude' ];
			}
		}

		$system_center = array();
		$system_center[ 'longitude' ] = $sensor_array_count == 0 ? 0 : $longitude_total / $sensor_array_count;
		$system_center[ 'latitude' ] = $sensor_array_count == 0 ? 0 : $latitude_total / $sensor_array_count;

		return $system_center;
	}

	public static function get_system( $system_uuid ) {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT name, description FROM systems WHERE uuid=?;" ) ) {
			$statement->bind_param( 's', $system_uuid );
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $name, $description );
			
			$statement->fetch();

			$system = array();
			$system[ 'uuid' ] = $system_uuid;
			$system[ 'name' ] = $name;
			$system[ 'description' ] = $description;

			return $system;
		}
	}

	public static function get_count() {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT COUNT(*) FROM systems;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $count );

			$statement->fetch();

			return $count;
		}
	}

	public static function print_system_list( $allow_management = false ) {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT uuid, name, description FROM systems;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $uuid, $name, $description );
			
			while ( $statement->fetch() ) {
				if ( $allow_management ) {
					if ( strlen( $description ) > 0 ) {
						echo "<img src='../static/img/remove.png' style='cursor: pointer' onclick='show_prompt(\"Remove system\", \"Are you sure you want to remove this system?<br><br>Sensor arrays and sensors under this system will also be removed\", \"../includes/services/systemRemove.php?uuid=$uuid\")'><a href='../system/$uuid'><p style='display: inline'> $name <span style='color: grey'> - $description</span></p></a><br>";
					} else {
						echo "<img src='../static/img/remove.png' style='cursor: pointer' onclick='show_prompt(\"Remove system\", \"Are you sure you want to remove this system?<br><br>Sensor arrays and sensors under this system will also be removed\", \"../includes/services/systemRemove.php?uuid=$uuid\")'><a href='../system/$uuid'><p style='display: inline'> $name</p></a><br>";
					}
				} else {
					if ( strlen( $description ) > 0 ) {
						echo "<a href='../system/$uuid'><p>$name <span style='color: grey'> - $description</span></p></a><br>";
					} else {
						echo "<a href='../system/$uuid'><p>$name</p></a><br>";
					}

					if ( $array_statement = $mysqli->prepare( "SELECT uuid, name, description FROM sensor_arrays WHERE system_uuid=?;" ) ) {
						$array_statement->bind_param( 's', $uuid );
						$array_statement->execute();
						$array_statement->store_result();
						$array_statement->bind_result( $sensor_array_uuid, $sensor_array_name, $sensor_array_description );
						
						while ( $array_statement->fetch() ) {
							if ( strlen( $sensor_array_description ) > 0 ) {
								echo "<p style='margin-left: 16px'>&#8627; <a href='../array/$sensor_array_uuid'>$sensor_array_name <span style='color: grey'> - $sensor_array_description</span></p></a><br>";
							} else {
								echo "<p style='margin-left: 16px'>&#8627; <a href='../array/$sensor_array_uuid'>$sensor_array_name</p></a><br>";
							}
						}
					}
				}
			}
		}
	}

	public static function create_system_dropdown() {
		require( 'services/DatabaseConnect.php' );

		echo '<select style="height: 21px" name="system">';

		if ( $statement = $mysqli->prepare( "SELECT uuid, name FROM systems;" ) ) {
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