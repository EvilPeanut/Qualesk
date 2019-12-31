<?

require_once( 'classes/dashboardManager.php' );
DashboardManager::remove_dashboard( $_GET["uuid"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>