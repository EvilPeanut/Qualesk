<?

parse_str( substr( $_SERVER[ 'REQUEST_URI' ], strpos( $_SERVER[ 'REQUEST_URI' ], "?" ) + 1 ), $query );

$sensor_uuid = substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );
$sensor_readings = SensorManager::get_sensor_readings( $sensor_uuid );
$sensor = SensorManager::get_sensor( $sensor_uuid ); 

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
	<script src="https://www.amcharts.com/lib/4/core.js"></script>
	<script src="https://www.amcharts.com/lib/4/charts.js"></script>
	<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
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

			<script>
				/*
					Data from PHP
				*/
				var upper_urgent_boundary = <? echo $upper_urgent_boundary != NULL ? $upper_urgent_boundary : 'null' ?>;
				var upper_warning_boundary = <? echo $upper_warning_boundary != NULL ? $upper_warning_boundary : 'null' ?>;
				var lower_warning_boundary = <? echo $lower_warning_boundary != NULL ? $lower_warning_boundary : 'null' ?>;
				var lower_urgent_boundary = <? echo $lower_urgent_boundary != NULL ? $lower_urgent_boundary : 'null' ?>;
				var unit = "<? echo $sensor[ 'unit' ] != NULL ? $sensor[ 'unit' ] : '' ?>";

				/*
					Charts
				*/
				am4core.ready(function() {

				am4core.useTheme(am4themes_animated);

				var chart = am4core.create("chartdiv", am4charts.XYChart);
				window.chart = chart;

				// Add data
				chart.data = [
				<?

				foreach ( $sensor_readings as $uuid => $sensor_reading ) {
					echo "{ date: new Date( Date.parse( '" . $sensor_reading[ 'date' ] . "' ) ), value: " . $sensor_reading[ 'data' ] . "},";
				}

				?>
				];

				// Set input format for the dates
				chart.dateFormatter.inputDateFormat = "yyyy-MM-dd";

				// Create axes
				var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
				dateAxis.renderer.grid.template.location = 0;
				dateAxis.minZoomCount = 5;
				dateAxis.groupData = true;
				dateAxis.groupCount = 500;

				var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

				// Create series
				var series = chart.series.push(new am4charts.LineSeries());
				series.dataFields.valueY = "value";
				series.dataFields.dateX = "date";
				series.tooltipText = "{value} <? echo $sensor[ 'unit' ]; ?>"
				series.strokeWidth = 2;
				series.minBulletDistance = 15;

				// Drop-shaped tooltips
				series.tooltip.background.cornerRadius = 20;
				series.tooltip.background.strokeOpacity = 0;
				series.tooltip.pointerOrientation = "vertical";
				series.tooltip.label.minWidth = 40;
				series.tooltip.label.minHeight = 40;
				series.tooltip.label.textAlign = "middle";
				series.tooltip.label.textValign = "middle";

				// Make bullets grow on hover
				var bullet = series.bullets.push(new am4charts.CircleBullet());
				bullet.circle.strokeWidth = 2;
				bullet.circle.radius = 1.25;
				bullet.circle.fill = am4core.color("#fff");

				var bullethover = bullet.states.create("hover");
				bullethover.properties.scale = 2;

				// Make a panning cursor
				chart.cursor = new am4charts.XYCursor();
				chart.cursor.behavior = "panXY";
				chart.cursor.xAxis = dateAxis;
				chart.cursor.snapToSeries = series;

				// Create vertical scrollbar and place it before the value axis
				chart.scrollbarY = new am4core.Scrollbar();
				chart.scrollbarY.parent = chart.leftAxesContainer;
				chart.scrollbarY.toBack();

				// Create a horizontal scrollbar with previe and place it underneath the date axis
				chart.scrollbarX = new am4charts.XYChartScrollbar();
				chart.scrollbarX.series.push(series);
				chart.scrollbarX.parent = chart.bottomAxesContainer;

				dateAxis.start = 0.79;
				dateAxis.keepSelection = true;

				// Create a range to change stroke for values below 0
				if ( upper_urgent_boundary ) {
					var upper_urgent_boundary_range = valueAxis.createSeriesRange(series);
					upper_urgent_boundary_range.value = upper_urgent_boundary;
					upper_urgent_boundary_range.endValue = Number.MAX_VALUE;
					upper_urgent_boundary_range.contents.stroke = chart.colors.getIndex(9);
					upper_urgent_boundary_range.contents.strokeOpacity = 0.7;
				}

				if ( upper_warning_boundary ) {
					var upper_warning_boundary_range = valueAxis.createSeriesRange(series);
					upper_warning_boundary_range.value = upper_warning_boundary;
					upper_warning_boundary_range.endValue = upper_urgent_boundary ? upper_urgent_boundary : Number.MAX_VALUE;
					upper_warning_boundary_range.contents.stroke = chart.colors.getIndex(11);
					upper_warning_boundary_range.contents.strokeOpacity = 0.7;
				}

				if ( lower_warning_boundary ) {
					var lower_warning_boundary_range = valueAxis.createSeriesRange(series);
					lower_warning_boundary_range.value = lower_warning_boundary;
					lower_warning_boundary_range.endValue = lower_urgent_boundary ? lower_urgent_boundary : -Number.MIN_VALUE;
					lower_warning_boundary_range.contents.stroke = chart.colors.getIndex(11);
					lower_warning_boundary_range.contents.strokeOpacity = 0.7;
				}

				if ( lower_urgent_boundary ) {
					var lower_urgent_boundary_range = valueAxis.createSeriesRange(series);
					lower_urgent_boundary_range.value = lower_urgent_boundary;
					lower_urgent_boundary_range.endValue = -Number.MIN_VALUE;
					lower_urgent_boundary_range.contents.stroke = chart.colors.getIndex(9);
					lower_urgent_boundary_range.contents.strokeOpacity = 0.7;
				}

				});

				/*
					WebSockets
				*/
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

						if ( json.type === 'sensor_reading' ) {
							window.chart.addData( [ { date: new Date( Date.parse( json.date ) ), value: json.data } ] );

							$( "#sensor_readings" ).prepend( "<p>" + json.date + " = " + json.data + " " + unit + "</p>" );
							$( "#reading_total" ).text( $( "#sensor_readings > p" ).length + " total" );
						}
					}
				}

				connect();
			</script>

			<div id="chartdiv"></div>
		</div></div>

		<div class="grid-item"><div>
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