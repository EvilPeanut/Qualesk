<?

$sensor_uuid = substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );

$sensor_readings = SensorManager::get_sensor_readings( $sensor_uuid );
$sensor = SensorManager::get_sensor( $sensor_uuid );

$graph_url = "https://" . $_SERVER['SERVER_NAME'] . "/graph/" . substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );

?>
<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get( 'site_name' ); ?> | Sensor Overview</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
	<script src="../static/js/farbtastic.js"></script>
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

		<!-- Share prompt -->
		<div id="div_overlay_share" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; background-color: rgba(0, 0, 0, 0.6); display: none">
			<div id="div_prompt" style="display: block; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 32px; background-color: white; border-radius: 16px;">
				<h1>Share <? echo $sensor['name']; ?> Graph</h1>
				<?

				if ( !$sensor[ 'permission_public_graph' ] ) {
					echo "<p style='color: #ff4000'>Users must be logged in to view this graph</p><br>";
				}

				?>
				<p>Link</p>
				<a href="<? echo $graph_url; ?>"><p><? echo $graph_url; ?></p></a>
				<br>
				<p>HTML</p>
				<input type="text" value="<iframe src='<? echo $graph_url; ?>'></iframe>" style="width: 512px">
				<br><br>
				<div onclick="$( '#div_overlay_share' ).hide()" class="inline-button"><p>Close</p></div>
			</div>
		</div>
		<!-- Share prompt -->

		<!-- Settings prompt -->
		<div id="div_overlay_settings" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; background-color: rgba(0, 0, 0, 0.6); display: none">
			<div id="div_prompt" style="display: block; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 32px; background-color: white; border-radius: 16px;">
				<h1><? echo $sensor['name']; ?> Graph Settings</h1>
				<p>Publicly Visible</p>
				<input id="chk_permission_public_graph" type="checkbox" <? echo $sensor[ 'permission_public_graph' ] ? 'checked' : ''; ?>><p style="display: inline">Allow</p></input>
				<br><br>
				<p>Colour</p>
				<div id="colorpicker"></div>
				<input id="input_default_colour" type="hidden" value="<? echo $sensor[ 'default_colour']; ?>">			
				<br>
				<script>
					$( function () {
						$( "#colorpicker" ).farbtastic();

						$.farbtastic( "#colorpicker" ).setColor( "<? echo $sensor[ 'default_colour']; ?>" );

						$.farbtastic( "#colorpicker" ).linkTo( ( colour ) => {
							$( "#input_default_colour" ).val( colour );
						});
					});

					function setGraphSettings() {
						$.ajax({
							method: "POST",
							url: "../includes/services/sensorGraphSettingsSet.php",
							data: { 
								sensor_uuid: "<? echo $sensor_uuid ?>",
								default_colour: $( "#input_default_colour" ).val(),
								permission_public_graph: $( "#chk_permission_public_graph" ).prop( "checked" ) ? 1 : 0
							}
						}).done( () => {
							location.reload();
						});

						$( '#div_overlay_settings' ).hide();
					}
				</script>
				<div onclick="setGraphSettings()" class="inline-button"><p>Apply</p></div>
				<div onclick="$( '#div_overlay_settings' ).hide()" class="inline-button"><p>Close</p></div>
			</div>
		</div>
		<!-- Settings prompt -->

		<div class="grid-item grid-item-3x1"><div>
			<h1 style="display: inline"><? echo $sensor['name']; ?> Graph</h1>
			<img style="display: inline; float: right; cursor: pointer" src="../static/img/icon_settings.png" onclick="$( '#div_overlay_settings' ).show()" />
			<img style="display: inline; float: right; cursor: pointer; margin-right: 8px" src="../static/img/icon_share.png" onclick="$( '#div_overlay_share' ).show()" />

			<iframe src="../graph/<? echo substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 ); ?>" style="width: 100%; height: 520px; border: 0; margin-top: 8px"></iframe>
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

		include 'elements/bottomBar.php';

		?>
	</div>
</body>
</html>