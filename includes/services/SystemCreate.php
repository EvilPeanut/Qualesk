<?

require_once( 'classes/systemManager.php' );
SystemManager::create_system( $_POST["name"], $_POST["description"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>