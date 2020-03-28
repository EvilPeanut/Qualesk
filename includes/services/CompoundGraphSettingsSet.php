<?

require( 'services/DatabaseConnect.php' );

$compound_graph_uuid = $_REQUEST[ "compound_graph_uuid" ];
$permission_public_graph = $_REQUEST[ "permission_public_graph" ];

$mysqli->prepare("
	UPDATE `graphs`
	SET
	`permission_public_graph` = '$permission_public_graph'
	WHERE `uuid` = '$compound_graph_uuid';
")->execute();

?>