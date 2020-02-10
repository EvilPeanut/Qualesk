<?

	require_once( "classes/graphManager.php" );
	require_once( "classes/accountManager.php" );

	$compound_graph_uuid = substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );

	$graph = GraphManager::get_graph( $compound_graph_uuid );
	$graph_sensors = GraphManager::get_graph_readings( $graph );

	$is_logged_in = AccountManager::is_logged_in();

?>
<!DOCTYPE html>
<html style="height: 100%">
	<head>
		<style>
		p, h1, tspan {
			margin: 0;
			font-family: sans-serif;
		}
		</style>

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

			var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

			// Create series
			function createSeries( index, name, unit ) {
				var series = chart.series.push(new am4charts.LineSeries());
				window.series = series;

				series.name = name;

				series.dataFields.valueY = "value" + index;
				series.dataFields.dateX = "date" + index;
				series.tooltip.getFillFromObject = false;
				series.tooltip.background.fill = am4core.color("#00C3FF");
				series.tooltip.label.interactionsEnabled = true;

				series.tooltipHTML = "<p style=\'margin-top: 4px\'>{value" + index + "} " + unit + "</p>";

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
			}

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

			// Create vertical scrollbar and place it before the value axis
			chart.scrollbarY = new am4core.Scrollbar();
			chart.scrollbarY.parent = chart.leftAxesContainer;
			chart.scrollbarY.toBack();

			// Create a horizontal scrollbar with preview and place it underneath the date axis
			chart.scrollbarX = new am4charts.XYChartScrollbar();
			chart.scrollbarX.series.push(window.series);
			chart.scrollbarX.parent = chart.bottomAxesContainer;

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
	<body style="height: 100%; margin: 0px; overflow: hidden">
		<div id="chartdiv" style="height: 100%"></div>
	</body>
</html>