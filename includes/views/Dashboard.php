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

		function addGraph( name, uuid ) {
			$( "#div_add" ).before( "<div id='element" + elementCount + "' class='grid-item grid-item-1x1'><div><h1 style='display: inline'>" + name + " Graph</h1><img class='icon' src='../static/img/icon_close.png' onclick='removeElement(" + elementCount + ")'/><iframe src='../graph/" + uuid + "' style='width: 100%; height: 520px; border: 0; margin-top: 8px'></iframe></div></div>" );

			$.ajax({
				method: "POST",
				url: "../includes/services/ajaxDashboardAdd.php",
				data: { dashboard_uuid: "<? echo $dashboard_uuid; ?>", element_type: "Graph", element_uuid: uuid }
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
				if ( $element->type == "Graph" ) {
					echo "<div id='element" . $key . "' class='grid-item grid-item-1x1'><div><h1 style='display: inline'>" . SensorManager::get_sensor( $element->uuid )[ 'name' ] . " Graph</h1><img class='icon' src='../static/img/icon_close.png' onclick='removeElement(" . $key . ", \"" . $element->uuid . "\")'/><iframe src='../graph/" . $element->uuid . "' style='width: 100%; height: 520px; border: 0; margin-top: 8px'></iframe></div></div>";
				}
			}
		}

		?>

		<div id="div_add" class="grid-item grid-item-1x1" style="background: none; border: 2px dashed rgba(255, 255, 255, 0.5); position: relative; min-height: 160px"><div>
			<img id="icon_add" src="../static/img/tile_add_graph.png" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); cursor: pointer" onclick="showAddElements()"/>
			<div id="div_add_items" style="display: none">
				<h1 style='display: inline'>Sensor Graphs</h1>
				<img class='icon' src='../static/img/icon_close.png' onclick="hideAddElements()"/>
				<div style="margin-top: 16px; max-height: 512px; overflow-y: auto">
				<?

				$sensor_list = SensorManager::get_sensor_list();

				$previous_sensor_array_name = null;

				foreach ( $sensor_list as $sensor_uuid => $sensor ) {
					if ($previous_sensor_array_name != $sensor[ 'sensor_array' ][ 'name' ]) {
						echo "<p style='margin-bottom: 4px;" . ( is_null( $previous_sensor_array_name ) ? "" : "margin-top: 16px" ) . "'>" . $sensor[ 'sensor_array' ][ 'name' ] . "</p>";

						$previous_sensor_array_name = $sensor[ 'sensor_array' ][ 'name' ];
					}

					echo "<a><p style='margin-left: 16px; margin-bottom: 4px' onclick='addGraph(\"" . $sensor[ 'name' ] . "\", \"" . $sensor_uuid . "\")'><span style='color: #7bbdff'>&#8627;</span> " . $sensor[ 'name' ] . "</p></a>";
				}

				?>
				</div>
			</div>
		</div></div>
		
		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>