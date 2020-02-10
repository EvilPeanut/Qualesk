<?

$compound_graph_uuid = substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );

$graph = GraphManager::get_graph( $compound_graph_uuid );

$graph_url = "https://" . $_SERVER['SERVER_NAME'] . "/compound/" . substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );

?>
<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get( 'site_name' ); ?> | Compound Graph Overview</title>
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

		<!-- Share prompt -->
		<div id="div_overlay_share" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; background-color: rgba(0, 0, 0, 0.6); display: none">
			<div id="div_prompt" style="display: block; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 32px; background-color: white; border-radius: 16px;">
				<h1>Share <? echo $graph['name']; ?> Graph</h1>
				<?

				// TODO: Permissions
				/*if ( !$sensor[ 'permission_public_graph' ] ) {
					echo "<p style='color: #ff4000'>Users must be logged in to view this graph</p><br>";
				}*/

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
				<h1><? echo $graph['name']; ?> Graph Settings</h1>
				<p>Publicly Visible</p>
				<input id="chk_permission_public_graph" type="checkbox" <? echo $graph[ 'permission_public_graph' ] ? 'checked' : ''; ?>><p style="display: inline">Allow</p></input>		
				<br>
				<script>
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

		<!-- -->
		<div id="div_overlay_sensors" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; background-color: rgba(0, 0, 0, 0.6); display: none">
			<div id="div_prompt" style="display: block; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 32px; background-color: white; border-radius: 16px;">
				<h1>Add Sensors to Graph</h1>
				<div style="margin-top: 16px; max-height: 512px; overflow-y: auto">
				<?

				$sensor_list = SensorManager::get_sensor_list();

				foreach ( $sensor_list as $sensor_uuid => $sensor ) {
					echo "<a href='../includes/services/graphAddSensor.php?graph_uuid=" . $graph['uuid'] . "&sensor_uuid=" . $sensor_uuid . "'><p style='cursor: pointer; background-color: #48a4ff; color: white; padding: 8px 8px; border-bottom: 1px solid white'>" . $sensor[ 'sensor_array' ][ 'name' ] . " - " . $sensor[ 'name' ] . "</p></a>";
				}

				?>
				</div>
				<div onclick="$( '#div_overlay_sensors' ).hide()" class="inline-button"><p>Close</p></div>
			</div>
		</div>
		<!-- -->

		<div class="grid-item grid-item-3x1"><div>
			<h1 style="display: inline"><? echo $graph['name']; ?> Graph</h1>
			<img style="display: inline; float: right; cursor: pointer" src="../static/img/icon_add.png" onclick="$( '#div_overlay_sensors' ).show()" />
			<!--<img style="display: inline; float: right; cursor: pointer; margin-right: 8px" src="../static/img/icon_settings.png" onclick="$( '#div_overlay_settings' ).show()" />
			<img style="display: inline; float: right; cursor: pointer; margin-right: 8px" src="../static/img/icon_share.png" onclick="$( '#div_overlay_share' ).show()" />-->


			<iframe src="../compound/<? echo substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 ); ?>" style="width: 100%; height: 520px; border: 0; margin-top: 8px"></iframe>
		</div></div>

		<?

		include 'elements/bottomBar.php';

		?>
	</div>
</body>
</html>