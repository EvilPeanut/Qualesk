<?

require_once( 'services/uuid.php' );

class SensorManager
{

	public static function create_sensor( $name, $description, $sensor_type, $sensor_array ) {
		require( 'services/DatabaseConnect.php' );

		// Generate sensor uuid
		$sensor_uuid = guidv4();

		// Insert in to database and create table
		if ( $statement = $mysqli->prepare( "INSERT INTO sensors (uuid, sensor_array_uuid, sensor_type, name, description) VALUES (?, ?, ?, ?, ?)" ) ) {
			$statement->bind_param( 'sssss', $sensor_uuid, $sensor_array, $sensor_type, $name, $description );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}

		return $sensor_uuid;
	}

	public static function create_sensor_reading( $sensor_uuid, $date, $data ) {
		require( 'services/DatabaseConnect.php' );

		// Generate sensor reading uuid
		$sensor_reading_uuid = guidv4();

		// Get sensor type
		$sensor_type = self::get_sensor( $sensor_uuid )[ 'sensor_type' ];

		// Insert in to database and create table
		if ( $statement = $mysqli->prepare( "INSERT INTO `sensor_$sensor_type` (uuid, sensor_uuid, date, data) VALUES (?, ?, ?, ?)" ) ) {
			$statement->bind_param( 'ssss', $sensor_reading_uuid, $sensor_uuid, $date, $data );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}
	}

	public static function create_multiple_sensor_reading( $sensor_uuid, $data_array ) {
		require( 'services/DatabaseConnect.php' );

		// Get sensor type
		$sensor_type = self::get_sensor( $sensor_uuid )[ 'sensor_type' ];

		// Insert in to database and create table
		if ( $statement = $mysqli->prepare( "INSERT INTO `sensor_$sensor_type` (uuid, sensor_uuid, date, data) VALUES (?, ?, ?, ?)" ) ) {
			$statement->bind_param( 'ssss', $sensor_reading_uuid, $sensor_uuid, $date, $data );

			$mysqli->query("START TRANSACTION");

			foreach ( $data_array as $reading ) {
				// Generate sensor reading uuid
				$sensor_reading_uuid = guidv4();

				// Extract from array
				$date = $reading[ 0 ];
				$data = $reading[ 1 ];

				// Add statement to transaction
			    if ( !$statement->execute() ) {
					// TODO: Handle SQL error
				}
			}

			$mysqli->query("COMMIT");
		}
	}

	public static function remove_sensor( $uuid ) {
		require_once( 'services/DatabaseConnect.php' );

		// Remove database records
		if ( $statement = $mysqli->prepare( "SELECT sensor_type FROM sensors WHERE uuid=?;" ) ) {
			$statement->bind_param( 's', $uuid );
			$statement->execute();
			$statement->store_result();

			$statement->bind_result( $sensor_type_uuid );

			$statement->fetch();
		}

		if ( $statement = $mysqli->prepare( "DELETE FROM sensors WHERE uuid=?" ) ) {
			$statement->bind_param( 's', $uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}

		if ( $statement = $mysqli->prepare( "DELETE FROM `sensor_$sensor_type_uuid` WHERE sensor_uuid=?" ) ) {
			$statement->bind_param( 's', $uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}
	}

	public static function get_sensor( $sensor_uuid ) {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT sensor_array_uuid, sensor_type, name, description, upper_urgent_boundary, upper_warning_boundary, lower_warning_boundary, lower_urgent_boundary FROM sensors WHERE uuid='$sensor_uuid';" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $sensor_array_uuid, $sensor_type, $name, $description, $upper_urgent_boundary, $upper_warning_boundary, $lower_warning_boundary, $lower_urgent_boundary );
			$statement->fetch();

			$sensor = array();
			$sensor[ 'sensor_array_uuid' ] = $sensor_array_uuid;
			$sensor[ 'sensor_type' ] = $sensor_type;
			$sensor[ 'name' ] = $name;
			$sensor[ 'description' ] = $description;
			$sensor[ 'upper_urgent_boundary' ] = $upper_urgent_boundary;
			$sensor[ 'upper_warning_boundary' ] = $upper_warning_boundary;
			$sensor[ 'lower_warning_boundary' ] = $lower_warning_boundary;
			$sensor[ 'lower_urgent_boundary' ] = $lower_urgent_boundary;

			$sensor_type = self::get_sensor_type( $sensor_type );
			$sensor[ 'unit' ] = $sensor_type[ 'unit' ];
		}

		return $sensor;
	}

	public static function get_sensor_type( $sensor_type_uuid ) {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT name, description, unit FROM sensor_types WHERE uuid='$sensor_type_uuid';" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $name, $description, $unit );
			$statement->fetch();

			$sensor_type = array();
			$sensor_type[ 'name' ] = $name;
			$sensor_type[ 'description' ] = $description;
			$sensor_type[ 'unit' ] = $unit;
		}

		return $sensor_type;
	}

	public static function get_sensor_readings( $sensor_uuid, $filter_date_min = NULL, $filter_date_max = NULL ) {
		require( 'services/DatabaseConnect.php' );

		$sensor = self::get_sensor( $sensor_uuid );
		$sensor_type_uuid = $sensor[ 'sensor_type' ];
		$sensor_type = self::get_sensor_type( $sensor_type_uuid );

		$filter = "";
		if ( $filter_date_min != NULL & $filter_date_max != NULL ) {
			$filter = "AND date BETWEEN '$filter_date_min' AND '$filter_date_max'";
		}

		if ( $statement = $mysqli->prepare( "SELECT uuid, CONCAT(date), data FROM `sensor_$sensor_type_uuid` WHERE sensor_uuid='$sensor_uuid' $filter ORDER BY date;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $uuid, $date, $data );

			$sensor_readings = array();
			
			while ( $statement->fetch() ) {
				$sensor_readings[ $uuid ] = array();
				$sensor_readings[ $uuid ][ 'date' ] = $date;
				$sensor_readings[ $uuid ][ 'data' ] = $data;
				$sensor_readings[ $uuid ][ 'name' ] = $sensor_type[ 'name' ];
				$sensor_readings[ $uuid ][ 'unit' ] = $sensor_type[ 'unit' ];
				$sensor_readings[ $uuid ][ 'sensor_type_uuid' ] = $sensor_type_uuid;
				$sensor_readings[ $uuid ][ 'upper_urgent_boundary' ] = $sensor[ 'upper_urgent_boundary' ];
				$sensor_readings[ $uuid ][ 'upper_warning_boundary' ] = $sensor[ 'upper_warning_boundary' ];
				$sensor_readings[ $uuid ][ 'lower_warning_boundary' ] = $sensor[ 'lower_warning_boundary' ];
				$sensor_readings[ $uuid ][ 'lower_urgent_boundary' ] = $sensor[ 'lower_urgent_boundary' ];
			}
		}

		return $sensor_readings;
	}

	public static function get_count( $sensor_array_uuid = NULL ) {
		require( 'services/DatabaseConnect.php' );

		$where_clause = "";

		if ( $sensor_array_uuid != NULL ) {
			$where_clause = " WHERE sensor_array_uuid='$sensor_array_uuid'";
		}

		if ( $statement = $mysqli->prepare( "SELECT COUNT(*) FROM sensors $where_clause;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $count );

			$statement->fetch();

			return $count;
		}
	}

	public static function get_reading_count( $sensor_uuid, $sensor_type_uuid ) {
		if ( $sensor_type_uuid == NULL ) {
			return 0;
		}

		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT COUNT(*) FROM `sensor_$sensor_type_uuid` WHERE sensor_uuid='$sensor_uuid';" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $count );

			$statement->fetch();

			return $count;
		}
	}

	public function set_reading_boundaries( $sensor_uuid, $upper_urgent_boundary, $upper_warning_boundary, $lower_warning_boundary, $lower_urgent_boundary ) {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "UPDATE sensors SET upper_urgent_boundary = ?, upper_warning_boundary = ?, lower_warning_boundary = ?, lower_urgent_boundary = ? WHERE uuid=?" ) ) {
			$statement->bind_param( 'sssss', $upper_urgent_boundary, $upper_warning_boundary, $lower_warning_boundary, $lower_urgent_boundary, $sensor_uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}
	}

	public static function print_sensor_list( $allow_management = false, $sensor_array_uuid = NULL ) {
		require( 'services/DatabaseConnect.php' );

		$where_clause = "";

		if ( $sensor_array_uuid != NULL ) {
			$where_clause = " WHERE sensor_array_uuid='$sensor_array_uuid'";
		}

		if ( $statement = $mysqli->prepare( "SELECT uuid, name, description FROM sensors $where_clause;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $uuid, $name, $description );
			
			while ( $statement->fetch() ) {
				if ( $allow_management ) {
					echo "<img src='../static/img/remove.png' style='cursor: pointer' onclick='show_prompt(\"Remove sensor\", \"Are you sure you want to remove this sensor?\", \"../includes/services/sensorRemove.php?uuid=$uuid\")'><a href='../sensor/$uuid'><p style='display: inline'> $name <span style='color: grey'> - $description</span></p></a><br>";
				} else {
					echo "<a href='../sensor/$uuid'><p>$name <span style='color: grey'> - $description</span></p></a><br>";
				}
			}
		}
	}

	public static function create_sensor_type( $name, $description, $data_type, $data_unit ) {
		require( 'services/DatabaseConnect.php' );

		// Generate user uuid
		$sensor_type_uuid = guidv4();

		// Insert in to database and create table
		if ( $statement = $mysqli->prepare( "INSERT INTO sensor_types (uuid, name, description, unit) VALUES (?, ?, ?, ?)" ) ) {
			$statement->bind_param( 'ssss', $sensor_type_uuid, $name, $description, $data_unit );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}

		$mysqli->prepare("
			CREATE TABLE `sensor_$sensor_type_uuid` (
			`uuid` char(36) NOT NULL,
			`sensor_uuid` char(36) NOT NULL,
			`date` datetime(3) NOT NULL,
			`data` $data_type NOT NULL,
			PRIMARY KEY (`uuid`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		")->execute();

		return $sensor_type_uuid;
	}

	public static function create_sensor_type_dropdown() {
		require( 'services/DatabaseConnect.php' );

		echo '<select name="sensor_type">';

		if ( $statement = $mysqli->prepare( "SELECT uuid, name FROM sensor_types;" ) ) {
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