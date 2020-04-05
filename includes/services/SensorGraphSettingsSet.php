<?

require( 'classes/configurable.php' );

$config = new Configurable( "sensors", $_REQUEST[ "sensor_uuid" ] );
$config->set( "default_colour", $_REQUEST[ "default_colour" ] );
$config->set( "permission_public_graph", $_REQUEST[ "permission_public_graph" ] );
$config->set( "adaptive_scale", $_REQUEST[ "adaptive_scale" ] );
$config->save();

?>