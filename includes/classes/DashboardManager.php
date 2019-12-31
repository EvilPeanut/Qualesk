<?

require_once( 'services/uuid.php' );

class DashboardManager
{

	public static function create_dashboard( $name, $description ) {
		require_once( 'services/DatabaseConnect.php' );

		// Generate dashboard uuid
		$uuid = guidv4();

		// Insert in to database and create table
		if ( $statement = $mysqli->prepare( "INSERT INTO dashboards (uuid, name, description) VALUES (?, ?, ?)" ) ) {
			$statement->bind_param( 'sss', $uuid, $name, $description );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}
	}

	public static function remove_dashboard( $uuid ) {
		require_once( 'services/DatabaseConnect.php' );

		// Remove database records
		if ( $statement = $mysqli->prepare( "DELETE FROM dashboards WHERE uuid=?" ) ) {
			$statement->bind_param( 's', $uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}
	}

	public static function get_dashboard( $uuid ) {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT name, description, dash_json FROM dashboards WHERE uuid=?;" ) ) {
			$statement->bind_param( 's', $uuid );
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $name, $description, $dash_json );
			
			$statement->fetch();

			$dashboard = array();
			$dashboard[ 'uuid' ] = $uuid;
			$dashboard[ 'name' ] = $name;
			$dashboard[ 'description' ] = $description;
			$dashboard[ 'data' ] = json_decode( $dash_json );

			return $dashboard;
		}
	}

	public static function get_count() {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT COUNT(*) FROM dashboards;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $count );

			$statement->fetch();

			return $count;
		}
	}

	public static function print_list( $allow_management = false ) {
		require( 'services/DatabaseConnect.php' );
		
		if ( $statement = $mysqli->prepare( "SELECT uuid, name, description, dash_json FROM dashboards;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $uuid, $name, $description, $dash_json );

			while ( $statement->fetch() ) {
				if ( $allow_management ) {
					if ( strlen( $description ) > 0 ) {
						echo "<img src='../static/img/remove.png' style='cursor: pointer' onclick='show_prompt(\"Remove dashboard\", \"Are you sure you want to remove this dashboard?\", \"../includes/services/dashboardRemove.php?uuid=$uuid\")'><a href='../dashboard/$uuid'><p style='display: inline-block; margin: 0px 0px 8px 8px'> $name <span style='color: grey'> - $description</span></p></a><br>";
					} else {
						echo "<img src='../static/img/remove.png' style='cursor: pointer' onclick='show_prompt(\"Remove dashboard\", \"Are you sure you want to remove this dashboard?\", \"../includes/services/dashboardRemove.php?uuid=$uuid\")'><a href='../dashboard/$uuid'><p style='display: inline-block; margin: 0px 0px 8px 8px'> $name</p></a><br>";
					}
				} else {
					if ( strlen( $description ) > 0 ) {
						echo "<p><a href='../dashboard/$uuid'>$name <span style='color: grey'> - $description</span></p></a><br>";
					} else {
						echo "<p><a href='../dashboard/$uuid'>$name</p></a><br>";
					}
				}
			}
		}
	}

}

?>