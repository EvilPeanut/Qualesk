<?

	$dashboard_uuid = substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );

	$dashboard = DashboardManager::get_dashboard( $dashboard_uuid );

?>
<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get( 'site_name' ); ?> | <? echo $dashboard[ 'name' ]; ?></title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<style>
		/* Hack to allow 2 columns */
		.grid-item-3x1 {
			grid-column-end: 3;
		}

		/* Set container to 2 columns */
		.grid-container {
			grid-template-columns: repeat(2, 1fr);
		}
	</style>
	<script src="../static/js/jquery-3.4.0.min.js"></script>
	<script>
		var elementCount = <? echo is_array( $dashboard[ 'data' ] ) ? count( $dashboard[ 'data' ] ) : 0; ?>;

		function removeElement( id, uuid ) {
			$( "#element" + id ).remove();

			$.ajax({
				method: "POST",
				url: "../includes/services/ajaxDashboardRemove.php",
				data: { dashboard_uuid: "<? echo $dashboard_uuid; ?>", element_uuid: uuid }
			});
		}

		function addGraph( name, uuid, type = "Graph" ) {
			let iframe_source = type == "Graph" ? "../graph/" + uuid : "../compound/" + uuid;

			$( "#div_add" ).before( "<div id='element" + elementCount + "' class='grid-item grid-item-1x1'><div><h1 style='display: inline'>" + name + " Graph</h1><img class='icon' src='../static/img/icon_close.png' onclick='removeElement(" + elementCount + ")'/><iframe src='" + iframe_source + "' style='width: 100%; height: 520px; border: 0; margin-top: 8px'></iframe></div></div>" );

			$.ajax({
				method: "POST",
				url: "../includes/services/ajaxDashboardAdd.php",
				data: { dashboard_uuid: "<? echo $dashboard_uuid; ?>", element_type: type, element_uuid: uuid }
			}).done(function( msg ) {
				$( "body" ).append( msg );
			});

			elementCount++;
		}

		function showAddElements() {
			$( "#div_add" ).css( "background", "white" );
			$( "#div_add_items" ).show();
			$( "#icon_add" ).hide();
		}

		function hideAddElements() {
			$( "#div_add" ).css( "background", "none" );
			$( "#div_add_items" ).hide();
			$( "#icon_add" ).show();
		}
	</script>
</head>
<body>
	<div class="grid-container">
		<?

		include 'elements/topBar.php'; 

		if ( is_array( $dashboard[ 'data' ] ) && count( $dashboard[ 'data' ] ) != 0 ) {
			foreach ( $dashboard[ 'data' ] as $key => $element ) {
				$source_url = "";
				$title = "";

				if ( $element->type == "Graph" ) {
					$source_url = "../graph/" . $element->uuid;
					$title = SensorManager::get_sensor( $element->uuid )[ 'name' ];
				} else if ( $element->type == "CompoundGraph" ) {
					$source_url = "../compound/" . $element->uuid;
					$title = GraphManager::get_graph( $element->uuid )[ 'name' ];
				}

				echo "<div id='element" . $key . "' class='grid-item grid-item-1x1'><div><h1 style='display: inline'>" . $title . " Graph</h1><img class='icon' src='../static/img/icon_close.png' onclick='removeElement(" . $key . ", \"" . $element->uuid . "\")'/><iframe src='" . $source_url . "' style='width: 100%; height: 520px; border: 0; margin-top: 8px'></iframe></div></div>";
			}
		}

		?>

		<div id="div_add" class="grid-item grid-item-1x1" style="background: none; border: 2px dashed rgba(255, 255, 255, 0.5); position: relative; min-height: 160px"><div>
			<img id="icon_add" src="../static/img/tile_add_graph.png" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); cursor: pointer" onclick="showAddElements()"/>
			<div id="div_add_items" style="display: none">
				<h1 style='display: inline'>Sensor Graphs</h1>
				<img class='icon' src='../static/img/icon_close.png' onclick="hideAddElements()"/>
				<div style="margin-top: 16px; max-height: 484px; overflow-y: auto">
				<?

				function compound_graph_list( $sensor_array_uuid ) {					
					$compound_graphs = GraphManager::get_graph_list( $sensor_array_uuid );

					foreach ( $compound_graphs as $compound_graph ) {
						echo "<a><p style='margin-left: 16px; margin-bottom: 4px' onclick='addGraph(\"" . $compound_graph[ 'name' ] . "\", \"" . $compound_graph[ 'uuid' ] . "\", \"CompoundGraph\")'><img src='../static/img/icon_compound_graph.png'> " . $compound_graph[ 'name' ] . "</p></a>";
					}
				}

				$sensor_list = SensorManager::get_sensor_list();

				$previous_sensor_array_uuid = null;

				foreach ( $sensor_list as $sensor_uuid => $sensor ) {
					if ( $previous_sensor_array_uuid != $sensor[ 'sensor_array' ][ 'uuid' ] ) {
						// Compound graphs
						if ( $previous_sensor_array_uuid != null ) {
							compound_graph_list( $previous_sensor_array_uuid );
						}

						// Sensor array title
						echo "<p style='margin-bottom: 4px;" . ( is_null( $previous_sensor_array_uuid ) ? "" : "margin-top: 16px" ) . "'>" . $sensor[ 'sensor_array' ][ 'name' ] . "</p>";

						$previous_sensor_array_uuid = $sensor[ 'sensor_array' ][ 'uuid' ];
					}

					echo "<a><p style='margin-left: 16px; margin-bottom: 4px' onclick='addGraph(\"" . $sensor[ 'name' ] . "\", \"" . $sensor_uuid . "\")'><img src='../static/img/icon_graph.png'> " . $sensor[ 'name' ] . "</p></a>";
				}

				if ( $previous_sensor_array_uuid != null ) {
					compound_graph_list( $previous_sensor_array_uuid );
				}

				?>
				</div>

				<br>

				<p><img src="../static/img/icon_graph.png"> Graph<img src="../static/img/icon_compound_graph.png" style="margin-left: 16px"> Compound Graph</p>
			</div>
		</div></div>
		
		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>