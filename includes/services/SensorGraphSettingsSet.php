<?

require( 'services/DatabaseConnect.php' );

$sensor_uuid = $_REQUEST[ "sensor_uuid" ];
$default_colour = $_REQUEST[ "default_colour" ];
$permission_public_graph = $_REQUEST[ "permission_public_graph" ];

$mysqli->prepare("
	UPDATE `sensors`
	SET
	`default_colour` = '$default_colour',
	`permission_public_graph` = '$permission_public_graph'
	WHERE `uuid` = '$sensor_uuid';
")->execute();

?>