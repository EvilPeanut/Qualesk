<?

require_once( 'services/uuid.php' );

class GraphManager
{

	public static function create_graph( $sensor_array_uuid, $name, $description ) {
		require( 'services/DatabaseConnect.php' );

		// Generate graph uuid
		$graph_uuid = guidv4();

		// Encode sensors array to json
		$sensors_json = json_encode( array() );

		// Insert in to database and create table
		if ( $statement = $mysqli->prepare( "INSERT INTO graphs (uuid, sensor_array_uuid, name, description, sensors) VALUES (?, ?, ?, ?, ?)" ) ) {
			$statement->bind_param( 'sssss', $graph_uuid, $sensor_array_uuid, $name, $description, $sensors_json );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}

		return $graph_uuid;
	}

	public static function get_graph( $graph_uuid ) {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT sensor_array_uuid, name, description, sensors FROM graphs WHERE uuid=?;" ) ) {
			$statement->bind_param( 's', $graph_uuid );
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $sensor_array_uuid, $name, $description, $sensors_json );
			
			$statement->fetch();

			$graph = array();
			$graph[ 'uuid' ] = $graph_uuid;
			$graph[ 'sensor_array_uuid' ] = $sensor_array_uuid;
			$graph[ 'name' ] = $name;
			$graph[ 'description' ] = $description;
			$graph[ 'sensors' ] = json_decode( $sensors_json );

			return $graph;
		}
	}

	public static function remove_graph( $graph_uuid ) {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "DELETE FROM graphs WHERE uuid=?" ) ) {
			$statement->bind_param( 's', $graph_uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}
	}

	public static function get_count( $sensor_array_uuid = NULL ) {
		require( 'services/DatabaseConnect.php' );

		$where_clause = "";

		if ( $sensor_array_uuid != NULL ) {
			$where_clause = " WHERE sensor_array_uuid='$sensor_array_uuid'";
		}

		if ( $statement = $mysqli->prepare( "SELECT COUNT(*) FROM graphs $where_clause;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $count );

			$statement->fetch();

			return $count;
		}
	}

	public static function get_graph_readings( $graph ) {
		$sensor_readings = array();

		foreach ( $graph['sensors'] as $sensor ) {
			array_push( $sensor_readings, SensorManager::get_sensor_readings( $sensor ) );
		}

		return $sensor_readings;
	}

	public static function add_graph_sensor( $graph_uuid, $sensor_uuid ) {
		require( 'services/DatabaseConnect.php' );

		$graph = GraphManager::get_graph( $graph_uuid );

		if ( count( $graph[ 'sensors' ] ) == 0 ) {
			$graph[ 'sensors' ] = array();
		}

		array_push( $graph[ 'sensors' ], $sensor_uuid );

		$sensors_json = json_encode( $graph[ 'sensors' ] );

		if ( $statement = $mysqli->prepare( "UPDATE graphs SET sensors = ? WHERE uuid = ?" ) ) {
			$statement->bind_param( 'ss', $sensors_json, $graph_uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}
	}

	public static function remove_graph_sensor( $graph_uuid, $sensor_uuid ) {
		require( 'services/DatabaseConnect.php' );

		$graph = GraphManager::get_graph( $graph_uuid );

		if ( ( $key = array_search( $sensor_uuid, $graph[ 'sensors' ] ) ) !== false ) {
			unset( $graph[ 'sensors' ][ $key ] );
		}

		if ( count( $graph[ 'sensors' ] ) == 0 ) {
			$graph[ 'sensors' ] = array();
		}

		$sensors_json = json_encode( $graph[ 'sensors' ] );

		if ( $statement = $mysqli->prepare( "UPDATE graphs SET sensors = ? WHERE uuid = ?" ) ) {
			$statement->bind_param( 'ss', $sensors_json, $graph_uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}
	}

	public static function print_graph_list( $allow_management = false, $sensor_array_uuid = NULL ) {
		require( 'services/DatabaseConnect.php' );

		$where_clause = "";

		if ( $sensor_array_uuid != NULL ) {
			$where_clause = " WHERE sensor_array_uuid='$sensor_array_uuid'";
		}

		if ( $statement = $mysqli->prepare( "SELECT uuid, name, description, sensor_array_uuid FROM graphs $where_clause ORDER BY sensor_array_uuid;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $uuid, $name, $description, $sensor_array_uuid );
			
			while ( $statement->fetch() ) {
				if ( $allow_management ) {
					if ( strlen( $description ) > 0 ) {
						echo "<img src='../static/img/remove.png' style='cursor: pointer' onclick='show_prompt(\"Remove compound graph\", \"Are you sure you want to remove this compound graph?\", \"../includes/services/graphRemove.php?uuid=$uuid\")'><a href='../collective/$uuid'><p style='display: inline-block; margin: 0px 0px 8px 8px'> $name <span style='color: grey'> - $description</span></p></a><br>";
					} else {
						echo "<img src='../static/img/remove.png' style='cursor: pointer' onclick='show_prompt(\"Remove compound graph\", \"Are you sure you want to remove this compound graph?\", \"../includes/services/graphRemove.php?uuid=$uuid\")'><a href='../collective/$uuid'><p style='display: inline-block; margin: 0px 0px 8px 8px'> $name</p></a><br>";
					}
				} else {
					if ( strlen( $description ) > 0 ) {
						echo "<a href='../collective/$uuid'><p>$name <span style='color: grey'> - $description</span></p></a><br>";
					} else {
						echo "<a href='../collective/$uuid'><p>$name</p></a><br>";
					}
				}
			}
		}
	}

}

?>