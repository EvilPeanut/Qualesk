<?

	require_once( "classes/graphManager.php" );
	require_once( "classes/accountManager.php" );

	$compound_graph_uuid = substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );

	$graph = GraphManager::get_graph( $compound_graph_uuid );
	$graph_sensors = GraphManager::get_graph_readings( $graph );

	$permission_public_graph = $graph[ 'permission_public_graph' ];

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
				Charts
			*/
			am4core.ready(function() {

			am4core.useTheme(am4themes_animated);

			var chart = am4core.create("chartdiv", am4charts.XYChart);
			window.chart = chart;

			// Add data
			chart.data = [
			<?

			$graph_index = 0;

			foreach ( $graph_sensors as $sensor ) {
				foreach ( $sensor as $uuid => $sensor_reading ) {
					echo "{ date" . $graph_index . ": new Date( Date.parse( '" . $sensor_reading[ 'date' ] . "' ) ), value" . $graph_index . ": " . $sensor_reading[ 'data' ] . "},";
				}

				$graph_index++;
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

			// Create a horizontal scrollbar with preview and place it underneath the date axis
			chart.scrollbarX = new am4charts.XYChartScrollbar();
			chart.scrollbarX.parent = chart.bottomAxesContainer;

			// Create series
			function createSeries( index, name, unit ) {
				var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

				if (chart.yAxes.indexOf(valueAxis) != 0) {
					valueAxis.syncWithAxis = chart.yAxes.getIndex(0);
				}

				var series = chart.series.push(new am4charts.LineSeries());
				window.series = series;

				series.name = name;

				series.yAxis = valueAxis;

				series.dataFields.valueY = "value" + index;
				series.dataFields.dateX = "date" + index;

				series.tooltip.getFillFromObject = false;
				series.tooltip.background.fill = series.stroke;
				series.tooltipText = "{value" + index + "} " + unit;

				// Drop-shaped tooltips
				series.tooltip.background.cornerRadius = 20;
				series.tooltip.background.strokeOpacity = 0;
				series.tooltip.pointerOrientation = "vertical";
				series.tooltip.label.minWidth = 40;
				series.tooltip.label.minHeight = 40;
				series.tooltip.label.textAlign = "middle";
				series.tooltip.label.textValign = "middle";

				series.strokeWidth = 2;
				series.minBulletDistance = 15;

				// Make bullets grow on hover
				var bullet = series.bullets.push(new am4charts.CircleBullet());
				bullet.circle.strokeWidth = 2;
				bullet.circle.radius = 1.25;
				bullet.circle.fill = am4core.color("#fff");

				var bullethover = bullet.states.create("hover");
				bullethover.properties.scale = 2;

				// y-Axis Options
				valueAxis.renderer.line.strokeOpacity = 1;
				valueAxis.renderer.line.strokeWidth = 2;
				valueAxis.renderer.line.stroke = series.stroke;
				valueAxis.renderer.labels.template.fill = series.stroke;

				// Add to date slider
				chart.scrollbarX.series.push(series);
			}

			// Make a panning cursor
			chart.cursor = new am4charts.XYCursor();
			chart.cursor.behavior = "panXY";
			chart.cursor.xAxis = dateAxis;
			chart.cursor.snapToSeries = window.series;

			<?

			$graph_index = 0;

			foreach ( $graph_sensors as $sensor_uuid => $sensor ) {
				echo 'createSeries(' . $graph_index . ', "' . SensorManager::get_sensor( $graph['sensors'][$graph_index] )['name'] . '", "' . $sensor[array_keys($sensor)[0]]['unit'] . '");';
				$graph_index++;
			}

			?>

			// Break date axis
			chart.events.on( 'inited', () => {
				var previousDate;
				var totalDateDifference = 0;

				for ( var dataIndex in chart.data ) {
					data = chart.data[dataIndex];

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

			dateAxis.start = 0.79;
			dateAxis.keepSelection = true;

			// Legend
			chart.legend = new am4charts.Legend();

			});

			/*
				WebSockets
			*/
			parent.$( parent.document ).on( "sensor_reading", ( event, date, data, reading_uuid ) => {
				window.chart.addData( [ { date: new Date( Date.parse( date ) ), value: data, uuid: reading_uuid } ] );
			} );
		</script>
	</head>
	<body class="iframe-graph-body">
		<div id="chartdiv"></div>
	</body>
</html>