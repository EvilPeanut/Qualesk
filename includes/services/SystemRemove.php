<?

require_once( 'classes/systemManager.php' );
SystemManager::remove_system( $_GET["uuid"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>