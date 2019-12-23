<?

	require_once( "classes/sensorManager.php" );
	require_once( "classes/accountManager.php" );

	$sensor_uuid = substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );

	$sensor_readings = SensorManager::get_sensor_readings( $sensor_uuid );
	$sensor = SensorManager::get_sensor( $sensor_uuid ); 

	$upper_urgent_boundary = $sensor[ 'upper_urgent_boundary' ];
	$upper_warning_boundary = $sensor[ 'upper_warning_boundary' ];
	$lower_warning_boundary = $sensor[ 'lower_warning_boundary' ];
	$lower_urgent_boundary = $sensor[ 'lower_urgent_boundary' ];

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
				Data from PHP
			*/
			var upper_urgent_boundary = <? echo $upper_urgent_boundary != NULL ? $upper_urgent_boundary : 'null' ?>;
			var upper_warning_boundary = <? echo $upper_warning_boundary != NULL ? $upper_warning_boundary : 'null' ?>;
			var lower_warning_boundary = <? echo $lower_warning_boundary != NULL ? $lower_warning_boundary : 'null' ?>;
			var lower_urgent_boundary = <? echo $lower_urgent_boundary != NULL ? $lower_urgent_boundary : 'null' ?>;

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
			parent.$( parent.document ).on( "sensor_reading", ( event, date, data ) => {
				window.chart.addData( [ { date: new Date( Date.parse( date ) ), value: data } ] );
			} );
		</script>
	</head>
	<body style="height: 100%; margin: 0px">
		<div id="chartdiv" style="height: 100%"></div>
	</body>
</html>