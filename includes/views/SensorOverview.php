<?

parse_str( substr( $_SERVER[ 'REQUEST_URI' ], strpos( $_SERVER[ 'REQUEST_URI' ], "?" ) + 1 ), $query );

$filter_date_min = array_key_exists( 'min-date', $query ) ? $query[ 'min-date' ] : NULL;
$filter_date_max = array_key_exists( 'max-date', $query ) ? $query[ 'max-date' ] : NULL;

if ( $filter_date_max == 'Now' ) {
	$filter_date_max = date( 'Y-m-d\TH:i:s', time() );
}

$sensor_uuid = substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );
$sensor_readings_raw = SensorManager::get_sensor_readings( $sensor_uuid );
$sensor_readings = SensorManager::get_sensor_readings( $sensor_uuid, $filter_date_min, $filter_date_max ); 
$sensor = SensorManager::get_sensor( $sensor_uuid ); 

$reading_date_min = str_replace( ' ', 'T', array_values( $sensor_readings_raw )[ 0 ][ 'date' ] ?? '' );
$reading_date_max = str_replace( ' ', 'T', array_values( $sensor_readings_raw )[ count( $sensor_readings_raw ) - 1 ][ 'date' ] ?? '' );

$filter_date_min = $filter_date_min == NULL ? $reading_date_min : $filter_date_min;
$filter_date_max = $filter_date_max == NULL ? $reading_date_max : $filter_date_max;

$upper_urgent_boundary = $sensor[ 'upper_urgent_boundary' ];
$upper_warning_boundary = $sensor[ 'upper_warning_boundary' ];
$lower_warning_boundary = $sensor[ 'lower_warning_boundary' ];
$lower_urgent_boundary = $sensor[ 'lower_urgent_boundary' ];

?>
<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get( 'site_name' ); ?> | Sensor Overview</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
	<script>
	var server_address = '<? echo Config::get( 'wsserver_host' ); ?>';
	var connection;

	function connect() {
		// Store and open the server websocket connection
		connection = new WebSocket( server_address );

		// When the connection is opened
		connection.onopen = () => {
			connection.send( JSON.stringify( {
				type: 'user_definition',
				data: {
					core_auth_key: '<? echo Config::get( 'wsserver_auth_key' ); ?>',
					user_uuid: '<? echo AccountManager::get_user_uuid() ?>',
					current_view: 'sensor',
					current_view_uuid: '<? echo $sensor_uuid; ?>'
				}
			} ) );
		};

		// Attempt reconnection every second
		connection.onclose = () => {
			setTimeout( () => {
				connect();
			}, 1000);
		};

		// When data is received
		connection.onmessage = ( message ) => {
		    var json = JSON.parse( message.data );

		    if ( !chartAPILoaded ) {
		    	//TODO: Store value for when API is ready
		    	return;
		    }

			if ( json.type === 'sensor_reading' ) {
				var style = 'point { size: 2; }';

				if ( json.data > upper_urgent_boundary || json.data < lower_urgent_boundary ) {
					style = 'point { fill-color: #FF553F; }';
				} else if ( json.data > upper_warning_boundary || json.data < lower_warning_boundary ) {
					style = 'point { fill-color: #FFEB3F; }';
				}

				reading_count++;
				reading_total += json.data;
				moving_average = reading_total / reading_count;

				if ( !init ) {
					data = google.visualization.arrayToDataTable([
						[ 'Timestamp', '<? echo $sensor[ 'unit' ]; ?>', { 'type': 'string', 'role': 'style' }, '<? echo $sensor[ 'unit' ]; ?> Moving Average' ],
						[ new Date( Date.parse( json.date ) ), json.data, style, moving_average ]
					]);

					drawChart( true );
				} else {
					data.addRow( [ new Date( Date.parse( json.date ) ), json.data, style, moving_average ] );

					chart.draw( data, options );
				}
			}
		}
	}

	connect();
	</script>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>

		<div class="grid-item grid-item-3x1"><div>
			<h1><? echo $sensor['name']; ?> Graph</h1>

			<script>
				var upper_urgent_boundary = <? echo $upper_urgent_boundary != NULL ? $upper_urgent_boundary : 'Number.MAX_SAFE_INTEGER' ?>;
				var upper_warning_boundary = <? echo $upper_warning_boundary != NULL ? $upper_warning_boundary : 'Number.MAX_SAFE_INTEGER' ?>;
				var lower_warning_boundary = <? echo $lower_warning_boundary != NULL ? $lower_warning_boundary : '-Number.MAX_SAFE_INTEGER' ?>;
				var lower_urgent_boundary = <? echo $lower_urgent_boundary != NULL ? $lower_urgent_boundary : '-Number.MAX_SAFE_INTEGER' ?>;
			</script>
			<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
			<script type="text/javascript">
				google.charts.load( 'current', { 'packages': [ 'corechart' ] } );
				google.charts.setOnLoadCallback( googleChartsLoaded );

				var data, container, chart, init = false, chartAPILoaded = false;
				var reading_total = 0, reading_count = 0;

				var options = {
					curveType: 'function',
					legend: { position: 'top', alignment: 'center' },
					pointsVisible: true,
					tooltip: { isHtml: true },
					trendlines: { 0: {
						lineWidth: 4,
						color: 'Grey',
						opacity: 0.2,
						pointsVisible: false,
						visibleInLegend: true,
						labelInLegend: 'Trend',
						tooltip: false
					} },
					series: {
						1: {
							lineWidth: 2,
							color: '#ADD8E6',
							pointsVisible: false
						}
					},
					chartArea: {
						width: '90%',
						height: '75%'
					}
				};

				function googleChartsLoaded() {
					chartAPILoaded = true;
					drawChart();
				}

				<?

				$reading_count = 0;
				$reading_total = 0;

				?>

				function drawChart() {
					if ( !data ) {
						data = google.visualization.arrayToDataTable([
							[ 'Timestamp', '<? echo $sensor[ 'unit' ]; ?>', { 'type': 'string', 'role': 'style' }, '<? echo $sensor[ 'unit' ]; ?> Moving Average' ],

							<?

							foreach ( $sensor_readings as $uuid => $sensor_reading ) {
								$style = "'point { size: 2; }'";

								if ( ( $upper_urgent_boundary != NULL && $sensor_reading[ 'data' ] > $upper_urgent_boundary ) || ( $lower_urgent_boundary != NULL && $sensor_reading[ 'data' ] < $lower_urgent_boundary ) ) {
									$style = "'point { fill-color: #FF553F; }'";
								} else if ( ( $upper_warning_boundary != NULL && $sensor_reading[ 'data' ] > $upper_warning_boundary ) || ( $lower_warning_boundary != NULL && $sensor_reading[ 'data' ] < $lower_warning_boundary ) ) {
									$style = "'point { fill-color: #FFEB3F; }'";
								}

								$reading_count++;
								$reading_total += $sensor_reading[ 'data' ];
								$moving_average = $reading_total / $reading_count;

								echo "[ new Date( Date.parse( '" . $sensor_reading[ 'date' ] . "' ) ), " . $sensor_reading[ 'data' ] . ", $style, " . $moving_average . " ],";
							}

							?>

						]);

						<?

						echo "reading_count = $reading_count;";
						echo "reading_total = $reading_total;";

						?>

					}

					if ( data.getNumberOfRows() != 0 ) {
						init = true;
					}

					container = document.getElementById( 'sensor_chart' );
					chart = new google.visualization.LineChart( container );

					google.visualization.events.addListener( chart, 'ready', () => {
						var layout = chart.getChartLayoutInterface();
						if ( $( "#sensor_chart_icons" ).length ) $( "#sensor_chart_icons" ).remove();
						$( "<div id='sensor_chart_icons'></div>" ).appendTo( "#sensor_chart" );
						var icon_container = document.getElementById( 'sensor_chart_icons' );

						for ( var i = 0; i < data.getNumberOfRows(); i++ ) {
							if ( data.getValue( i, 1 ) > upper_urgent_boundary || data.getValue( i, 1 ) < lower_urgent_boundary || data.getValue( i, 1 ) > upper_warning_boundary || data.getValue( i, 1 ) < lower_warning_boundary ) {
								var xPos = layout.getXLocation( data.getValue( i, 0 ) );
								var yPos = layout.getYLocation( data.getValue( i, 1 ) );

								var warningImg = icon_container.appendChild( document.createElement( 'img' ) );
								if ( data.getValue( i, 1 ) > upper_urgent_boundary || data.getValue( i, 1 ) < lower_urgent_boundary ) {
									warningImg.src = '../static/img/urgent.png';
								} else {
									warningImg.src = '../static/img/warning.png';
								}
								
								warningImg.style.position = 'absolute';
								warningImg.style.top = ( yPos + 164 ) + 'px';
								warningImg.style.left = ( xPos + 24 ) + 'px';
							}
						}
					});

					if ( init ) {
						chart.draw( data, options );
					}
				}
			</script>

			<div id="sensor_chart" style="width: 100%; height: 320px"></div>

			<center style="letter-spacing: 1px; padding-top: 4px">
				<p style="display: inline">From </p><input type="datetime-local" id="min-date" value="<? echo $filter_date_min ?>" min="<? echo $reading_date_min ?>" max="<? echo $reading_date_max ?>" style="border: 0; font-family: sans-serif; font-size: 16px; text-align: center">
				<p style="display: inline">To </p><input type="datetime-local" id="max-date" value="<? echo $filter_date_max ?>" min="<? echo $reading_date_min ?>" max="<? echo $reading_date_max ?>" style="border: 0; font-family: sans-serif; font-size: 16px; text-align: center">
				<input type="checkbox" value="max-date-now" id="checkbox-max-date-now" <? echo !array_key_exists( 'max-date', $query ) || $query[ 'max-date' ] == 'Now' ? 'checked' : '' ?> ><p style="display: inline">Now </p>
				<button id="button_date_filter" style="height: 24px">Filter</button>
				<script>
					$( "#max-date" ).prop( "disabled", $( "#checkbox-max-date-now" ).prop( "checked" ) );

					$( "#checkbox-max-date-now" ).change( () => {
						$( "#max-date" ).prop( "disabled", $( "#checkbox-max-date-now" ).prop( "checked" ) );
					} );

					$( "#button_date_filter" ).click( () => {
						var max_date = $( "#checkbox-max-date-now" ).prop( "checked" ) ? 'Now' : $( "#max-date" ).val();

						window.location.href = window.location.origin + window.location.pathname + "?min-date=" + $( "#min-date" ).val() + "&max-date=" + max_date;
					} );
				</script>
			</center>
		</div></div>

		<div class="grid-item"><div>
			<h1><? echo $sensor[ 'name' ]; ?> Readings <span style="color: grey; font-size: small"><? echo count( $sensor_readings ); ?> total</span></h1>
			<div style="height: 320px; overflow-y: auto">
				<?

				foreach ( $sensor_readings as $uuid => $sensor_reading ) {
					echo "<p>" . $sensor_reading[ 'date' ] . " = " . $sensor_reading[ 'data' ] . " " . $sensor_reading[ 'unit' ] . "</p>";
				}

				?>
			</div>
		</div></div>

		<div class="grid-item"><div>
			<h1>Import Sensor Reading</h1>
			<form action="../includes/services/sensorReadingImport.php" method="post" enctype="multipart/form-data">
				<input type="file" name="CSVfile" id="CSVfile">
				<br><br>
				<p>Sensor Name</p><input type="text" name="sensor_name" value="<? echo $sensor['name']; ?>"><br><br>
				<input type="submit" value="Import Sensor Readings">
			</form>
			<br>
			<h1>Add Sensor Reading</h1>
			<form action="../includes/services/sensorReadingCreate.php" method="post">
				<p>Date</p><input type="text" name="date"><br><br>
				<p>Data</p><input type="text" name="data"><br><br>
				<input type="submit" value="Create Sensor Reading">
			</form>
		</div></div>

		<div class="grid-item"><div>
			<h1>Sensor Reading Boundaries</h1>
			<form action="../includes/services/sensorBoundariesSet.php" method="post">
				<p>Upper Urgent Boundary</p><input type="text" name="upper_urgent_boundary" value="<? echo $upper_urgent_boundary; ?>"><br><br>
				<p>Upper Warning Boundary</p><input type="text" name="upper_warning_boundary" value="<? echo $upper_warning_boundary; ?>"><br><br>
				<p>Lower Warning Boundary</p><input type="text" name="lower_warning_boundary" value="<? echo $lower_warning_boundary; ?>"><br><br>
				<p>Lower Urgent Boundary</p><input type="text" name="lower_urgent_boundary" value="<? echo $lower_urgent_boundary; ?>"><br><br>
				<input type="submit" value="Set Boundaries">
			</form>
		</div></div>
	</div>
</body>
</html>