<?

require_once( 'classes/graphManager.php' );
GraphManager::remove_graph( $_GET["uuid"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>