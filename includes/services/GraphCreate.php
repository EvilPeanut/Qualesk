<?

require_once( 'classes/graphManager.php' );

if ( strlen( trim( $_POST["name"] ) ) != 0 ) {
	GraphManager::create_graph( $_POST["sensor_array_uuid"], $_POST["name"], $_POST["description"] );
}

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>