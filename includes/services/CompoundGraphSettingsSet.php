<?

require( 'classes/configurable.php' );

$config = new Configurable( "graphs", $_REQUEST[ "compound_graph_uuid" ] );
$config->set( "permission_public_graph", $_REQUEST[ "permission_public_graph" ] );
$config->set( "adaptive_scale", $_REQUEST[ "adaptive_scale" ] );
$config->save();

?>