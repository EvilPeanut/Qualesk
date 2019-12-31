<?

require_once( 'classes/dashboardManager.php' );

if ( strlen( trim( $_POST["name"] ) ) != 0 ) {
	DashboardManager::create_dashboard( $_POST["name"], $_POST["description"] );
}

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>