<?

$compound_graph_uuid = substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );

$graph = GraphManager::get_graph( $compound_graph_uuid );

$graph_url = "https://" . $_SERVER['SERVER_NAME'] . "/compound/" . substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );

function is_sensor_on_graph( $graph, $uuid ) {
	foreach ( $graph[ 'sensors' ] as $sensor ) {
		if ( $uuid == $sensor ) {
			return true;
		}
	}

	return false;
}

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
	<script>
		$( document ).ready( function() {
			$( 'input[type="checkbox"]' ).change( function() {
				let graph_uuid = '<? echo $compound_graph_uuid ?>';

				if ( this.checked ) {
					window.location.href = '../includes/services/graphAddSensor.php?graph_uuid=' + graph_uuid + '&sensor_uuid=' + $( this ).attr( 'sensor_uuid' );
				} else {
					window.location.href = '../includes/services/graphRemoveSensor.php?graph_uuid=' + graph_uuid + '&sensor_uuid=' + $( this ).attr( 'sensor_uuid' );
				}
			} );
		} );
	</script>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>

		<!-- Share prompt -->
		<div id="div_overlay_share" class="overlay">
			<div id="div_prompt">
				<h1>Share <? echo $graph['name']; ?> Graph</h1>

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
		<div id="div_overlay_settings" class="overlay">
			<div id="div_prompt">
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

		<div class="grid-item grid-item-3x1"><div>
			<h1 class="graph-title"><? echo $graph['name']; ?> Graph</h1>

			<div>
				<div style="width: 80%; float: left">
					<iframe src="../compound/<? echo substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 ); ?>" style="width: 100%; height: calc(100vh - 178px); border: 0; margin-top: 8px"></iframe>
				</div>

				<div style="width: 19%; height: calc(100vh - 178px); border: 0; margin-top: 12px; margin-left: 81%; overflow-y: auto">
					<?

					$sensor_list = SensorManager::get_sensor_list();

					$previous_sensor_array_name = null;

					foreach ( $sensor_list as $sensor_uuid => $sensor ) {
						if ($previous_sensor_array_name != $sensor[ 'sensor_array' ][ 'name' ]) {
							echo "<p style='margin-bottom: 4px;" . ( is_null( $previous_sensor_array_name ) ? "" : "margin-top: 16px" ) . "'>" . $sensor[ 'sensor_array' ][ 'name' ] . "</p>";

							$previous_sensor_array_name = $sensor[ 'sensor_array' ][ 'name' ];
						}

						echo "<p style='margin-left: 16px; margin-bottom: 4px'><span style='color: #7bbdff'>&#8627;</span> <input type='checkbox' sensor_uuid='" . $sensor_uuid . "' " . ( is_sensor_on_graph( $graph, $sensor_uuid ) ? "checked" : "" ) . ">" . $sensor[ 'name' ] . "</p>";
					}

					?>
				</div>
			</div>
		</div></div>

		<?

		include 'elements/bottomBar.php';

		?>
	</div>
</body>
</html>