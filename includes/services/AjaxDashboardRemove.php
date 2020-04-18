<?

require( 'services/DatabaseConnect.php' );
require( 'classes/DashboardManager.php' );

$dashboard_uuid = $_REQUEST[ "dashboard_uuid" ];
$element_uuid = $_REQUEST[ "element_uuid" ];

$dashboard = DashboardManager::get_dashboard( $dashboard_uuid );

if ( count( $dashboard[ 'data' ] ) == 1 ) {
	$dashboard[ 'data' ] = array();
} else {
	$uuid_index = -1;

	foreach ( $dashboard[ 'data' ] as $index => $element ) {
		if ( $element->uuid == $element_uuid ) {
			$uuid_index = $index;
			continue;
		}
	}

	array_splice( $dashboard[ 'data' ], $uuid_index, 1 );
}

$dash_json = json_encode( $dashboard[ 'data' ] );

if ( $statement = $mysqli->prepare( "UPDATE dashboards SET dash_json = ? WHERE uuid = ?" ) ) {
	$statement->bind_param( 'ss', $dash_json, $dashboard_uuid );
	if ( !$statement->execute() ) {
		// TODO: Handle SQL error
	}
}

?>