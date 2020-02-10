<?

require_once( 'classes/graphManager.php' );

GraphManager::remove_graph_sensor( $_GET["graph_uuid"], $_GET["sensor_uuid"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>