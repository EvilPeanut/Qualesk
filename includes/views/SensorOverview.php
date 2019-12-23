<?

$sensor_uuid = substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );

$sensor_readings = SensorManager::get_sensor_readings( $sensor_uuid );
$sensor = SensorManager::get_sensor( $sensor_uuid );

?>
<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get( 'site_name' ); ?> | Sensor Overview</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
	<? include 'services/websocketHandler.php'; ?>
	<style>
		#chartdiv {
		  width: 100%;
		  height: 420px;
		}
	</style>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>

		<div class="grid-item grid-item-3x1"><div>
			<h1><? echo $sensor['name']; ?> Graph</h1>

			<iframe src="../includes/elements/panels/sensorGraph.php?<? echo substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 ); ?>" style="width: 100%; height: 520px; border: 0"></iframe>
		</div></div>

		<div class="grid-item"><div>
			<script>
				/*
					WebSockets
				*/
				$( document ).on( "sensor_reading", ( event, date, data ) => {
					$( "#sensor_readings" ).prepend( "<p>" + date + " = " + data + " <? echo $sensor[ 'unit' ]; ?></p>" );
					$( "#reading_total" ).text( $( "#sensor_readings > p" ).length + " total" );
				} );
			</script>
			<h1><? echo $sensor[ 'name' ]; ?> Readings <span id="reading_total" style="color: grey; font-size: small"><? echo count( $sensor_readings ); ?> total</span></h1>
			<div id="sensor_readings" style="height: 320px; overflow-y: auto">
				<?

				foreach ( array_reverse( $sensor_readings ) as $uuid => $sensor_reading ) {
					echo "<p>" . $sensor_reading[ 'date' ] . " = " . $sensor_reading[ 'data' ] . " " . $sensor_reading[ 'unit' ] . "</p>";
				}

				?>
			</div>
		</div></div>

		<?
		
		if ( AccountManager::has_permission( 'admin_features' ) ) {
			include 'elements/panels/sensorAddReadings.php';
			include 'elements/panels/sensorSetBoundaries.php';
		}

		?>
	</div>
</body>
</html>