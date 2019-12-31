<?

require( 'services/DatabaseConnect.php' );
require( 'classes/DashboardManager.php' );

$dashboard_uuid = $_REQUEST[ "dashboard_uuid" ];
$element_type = $_REQUEST[ "element_type" ];
$element_uuid = $_REQUEST[ "element_uuid" ];

$dashboard = DashboardManager::get_dashboard( $dashboard_uuid );

if ( count( $dashboard[ 'data' ] ) == 0 ) {
	$dashboard[ 'data' ] = array();
}

array_push( $dashboard[ 'data' ], [ 'type' => $element_type, 'uuid' => $element_uuid ] );

$dash_json = json_encode( $dashboard[ 'data' ] );

if ( $statement = $mysqli->prepare( "UPDATE dashboards SET dash_json = ? WHERE uuid = ?" ) ) {
	$statement->bind_param( 'ss', $dash_json, $dashboard_uuid );
	if ( !$statement->execute() ) {
		// TODO: Handle SQL error
	}
}

?>