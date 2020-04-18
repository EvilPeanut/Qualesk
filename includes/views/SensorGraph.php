<?

	require_once( "classes/sensorManager.php" );
	require_once( "classes/accountManager.php" );
	require_once( 'classes/configurable.php' );

	$sensor_uuid = substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );

	$config = new Configurable( "sensors", $sensor_uuid );

	$sensor_readings = SensorManager::get_sensor_readings( $sensor_uuid );
	$sensor = SensorManager::get_sensor( $sensor_uuid ); 

	$upper_urgent_boundary = $sensor[ 'upper_urgent_boundary' ];
	$upper_warning_boundary = $sensor[ 'upper_warning_boundary' ];
	$lower_warning_boundary = $sensor[ 'lower_warning_boundary' ];
	$lower_urgent_boundary = $sensor[ 'lower_urgent_boundary' ];

	$default_colour = $config->get( 'default_colour', '#67B7DC' );
	$permission_public_graph = (boolean)$config->get( 'permission_public_graph', false );
	$adaptive_scale = (boolean)$config->get( 'adaptive_scale', false );

	$series_min = null;
	$series_max = null;

	$is_logged_in = AccountManager::is_logged_in();

	if ( !$permission_public_graph && !$is_logged_in ) {
		echo '<p>This graph is not visible to the public</p>';
		exit();
	}

?>
<!DOCTYPE html>
<html style="height: 100%">
	<head>
		<link rel="stylesheet" type="text/css" href="../static/css/main.css">

		<script src="https://www.amcharts.com/lib/4/core.js"></script>
		<script src="https://www.amcharts.com/lib/4/charts.js"></script>
		<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

		<script>
			/*
				Data from PHP
			*/
			var upper_urgent_boundary = <? echo $upper_urgent_boundary != NULL ? $upper_urgent_boundary : 'null' ?>;
			var upper_warning_boundary = <? echo $upper_warning_boundary != NULL ? $upper_warning_boundary : 'null' ?>;
			var lower_warning_boundary = <? echo $lower_warning_boundary != NULL ? $lower_warning_boundary : 'null' ?>;
			var lower_urgent_boundary = <? echo $lower_urgent_boundary != NULL ? $lower_urgent_boundary : 'null' ?>;
			var adaptive_scale = <? echo $adaptive_scale ? 'true' : 'false' ?>;

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
				$data = $sensor_reading[ 'data' ];

				if ( $series_min != null ) {
					if ( $series_min > $data ) {
						$series_min = $data;
					}
				} else {
					$series_min = $data;
				}

				if ( $series_max != null ) {
					if ( $series_max < $data ) {
						$series_max = $data;
					}
				} else {
					$series_max = $data;
				}

				echo "{ date: new Date( Date.parse( '" . $sensor_reading[ 'date' ] . "' ) ), value: " . $data . ", uuid: '" . $uuid . "', anomaly: " . $sensor_reading[ 'anomaly' ] . "},";
			}

			?>
			];

			// Create axes
			var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
			dateAxis.renderer.grid.template.location = 0;
			dateAxis.minZoomCount = 5;
			dateAxis.groupData = true;
			dateAxis.groupCount = 500;

			dateAxis.tooltipDateFormat = "yyyy-MM-dd\nhh:mm:ss";

			var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

			// Adaptive scale
			if ( !adaptive_scale ) {
				chart.events.on( "ready", () => {
					valueAxis.min = <? echo $series_min ?>;
					valueAxis.max = <? echo $series_max ?>;
				} );
			}

			// Create series
			var series = chart.series.push(new am4charts.LineSeries());
			window.series = series;
			series.dataFields.valueY = "value";
			series.dataFields.dateX = "date";
			series.stroke = am4core.color("<? echo $default_colour; ?>");
			series.tooltip.getFillFromObject = false;
			series.tooltip.background.fill = am4core.color("#00C3FF");
			series.tooltip.label.interactionsEnabled = true;

			<?

			if ( $is_logged_in ) {
				echo 'series.tooltipHTML = "<p>{value} ' . $sensor[ 'unit' ] . '</p><input type=\'checkbox\' id=\'chk_anomaly\' onclick=\'window.toggle_anomaly()\'><p>Anomalous reading</p></input>"';
			} else {
				echo 'series.tooltipHTML = "<p style=\'margin-top: 4px\'>{value} ' . $sensor[ 'unit' ] . '</p>"';
			}

			?>

			series.events.on( "tooltipshownat", ( event ) => {
				setTimeout(() => {
					if ( event.dataItem.dataContext.anomaly) {
						$( "#chk_anomaly" ).prop( "checked", true );
					} else {
						$( "#chk_anomaly" ).prop( "checked", false );
					}
				}, 100);
			});

			series.strokeWidth = 2;
			series.minBulletDistance = 15;

			// Break date axis and build anomaly record list
			var anomalies = [];

			chart.events.on( 'inited', () => {
				var previousDate;
				var totalDateDifference = 0;

				for ( var dataIndex in chart.data ) {
					data = chart.data[dataIndex];

					if ( data.anomaly ) {
						anomalies.push( dataIndex );
					}

					if ( dataIndex == 0 ) {
						previousDate = data.date;
						continue;
					}

					totalDateDifference += data.date - previousDate;

					previousDate = data.date;
				}

				var breakThreshold = ( totalDateDifference / chart.data.length ) * 2;

				for ( var dataIndex in chart.data ) {
					data = chart.data[dataIndex];

					if ( dataIndex == 0 ) {
						previousDate = data.date;
						continue;
					}

					if ( data.date - previousDate > breakThreshold ) {
						let dateAxisBreak = dateAxis.axisBreaks.create();
						dateAxisBreak.startDate = previousDate;
						dateAxisBreak.endDate = data.date;
						dateAxisBreak.breakSize = 0;
					}

					previousDate = data.date;
				}
			});

			series.events.on( "datarangechanged", ( event ) => {
				for ( var anomalyIndex in anomalies ) {
					anomalyIndex = anomalies[ anomalyIndex ];

					if ( anomalyIndex in series.dataItems.values && series.dataItems.values[ anomalyIndex ].sprites.length != 0 ) {
						series.dataItems.values[ anomalyIndex ].sprites[0].circle.radius = 5;
						series.dataItems.values[ anomalyIndex ].sprites[0].circle.stroke = chart.colors.getIndex(9);
					}
				}
			});

			window.toggle_anomaly = function() {
				if ( !series.tooltip.tooltipDataItem.dataContext.anomaly ) {
					series.tooltip.tooltipDataItem.sprites[0].circle.radius = 5;
					series.tooltip.tooltipDataItem.sprites[0].circle.stroke = chart.colors.getIndex(9);
				} else {
					series.tooltip.tooltipDataItem.sprites[0].circle.radius = 1.25;
					series.tooltip.tooltipDataItem.sprites[0].circle.stroke = chart.colors.getIndex(0);
				}

				series.tooltip.tooltipDataItem.dataContext.anomaly = !series.tooltip.tooltipDataItem.dataContext.anomaly;

				$.ajax({
					method: "POST",
					url: "../includes/services/toggleAnomalousReading.php",
					data: { 
						sensor_type_uuid: "<? echo $sensor[ 'sensor_type' ] ?>",
						reading_uuid: series.tooltip.tooltipDataItem.dataContext.uuid
					}
				});
			}

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

			// Create a horizontal scrollbar with preview and place it underneath the date axis
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
			}

			if ( upper_warning_boundary ) {
				var upper_warning_boundary_range = valueAxis.createSeriesRange(series);
				upper_warning_boundary_range.value = upper_warning_boundary;
				upper_warning_boundary_range.endValue = upper_urgent_boundary ? upper_urgent_boundary : Number.MAX_VALUE;
				upper_warning_boundary_range.contents.stroke = chart.colors.getIndex(11);
			}

			if ( lower_warning_boundary ) {
				var lower_warning_boundary_range = valueAxis.createSeriesRange(series);
				lower_warning_boundary_range.value = lower_warning_boundary;
				lower_warning_boundary_range.endValue = lower_urgent_boundary ? lower_urgent_boundary : -Number.MIN_VALUE;
				lower_warning_boundary_range.contents.stroke = chart.colors.getIndex(11);
			}

			if ( lower_urgent_boundary ) {
				var lower_urgent_boundary_range = valueAxis.createSeriesRange(series);
				lower_urgent_boundary_range.value = lower_urgent_boundary;
				lower_urgent_boundary_range.endValue = -Number.MIN_VALUE;
				lower_urgent_boundary_range.contents.stroke = chart.colors.getIndex(9);
			}

			});

			/*
				WebSockets
			*/
			$( document ).on( "sensor_reading", ( event, date, data, reading_uuid, sensor_uuid ) => {
				if ( sensor_uuid == "<? echo $sensor_uuid ?>" ) {
					window.chart.addData( [ { date: new Date( Date.parse( date ) ), value: data, uuid: reading_uuid, anomaly: 0 } ] );
				}
			} );
		</script>

		<? include 'services/websocketHandler.php'; ?>
	</head>
	<body class="iframe-graph-body">
		<div id="chartdiv"></div>
	</body>
</html>