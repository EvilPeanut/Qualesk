<?

require( 'services/DatabaseConnect.php' );

$sensor_type_uuid = $_REQUEST[ "sensor_type_uuid" ];
$reading_uuid = $_REQUEST[ "reading_uuid" ];

$mysqli->prepare("
	UPDATE `sensor_$sensor_type_uuid`
	SET `anomaly` = NOT `anomaly`
	WHERE `uuid` = '$reading_uuid';
")->execute();

?>