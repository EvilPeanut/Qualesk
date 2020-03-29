<?

	require_once( "classes/sensorManager.php" );
	require_once( "classes/accountManager.php" );

	$sensor_uuid = substr( $_GET[ 'url' ], strrpos( $_GET[ 'url' ], '/' ) + 1 );

	$sensor_readings = SensorManager::get_sensor_readings( $sensor_uuid );
	$sensor_last_reading = end( $sensor_readings )[ 'data' ];
	$sensor = SensorManager::get_sensor( $sensor_uuid ); 

	$upper_urgent_boundary = $sensor[ 'upper_urgent_boundary' ];
	$upper_warning_boundary = $sensor[ 'upper_warning_boundary' ];
	$lower_warning_boundary = $sensor[ 'lower_warning_boundary' ];
	$lower_urgent_boundary = $sensor[ 'lower_urgent_boundary' ];

	$default_colour = $sensor[ 'default_colour' ];
	$permission_public_graph = $sensor[ 'permission_public_graph' ];

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

			am4core.ready( function() {
				am4core.useTheme(am4themes_animated);

				/*
				 * Charts
				 */
				var chart = am4core.create("chartdiv", am4charts.GaugeChart);
				chart.hiddenState.properties.opacity = 0;
				chart.fontSize = 11;
				chart.innerRadius = am4core.percent(80);
				chart.resizable = true;

				/**
				 * Normal axis
				 */
				var axis = chart.xAxes.push(new am4charts.ValueAxis());
				axis.min = -1;
				axis.max = 1;
				axis.strictMinMax = true;
				axis.renderer.radius = am4core.percent(80);
				axis.renderer.inside = true;
				axis.renderer.line.strokeOpacity = 0.1;
				axis.renderer.ticks.template.disabled = false;
				axis.renderer.ticks.template.strokeOpacity = 1;
				axis.renderer.ticks.template.strokeWidth = 0.5;
				axis.renderer.ticks.template.length = 5;
				axis.renderer.grid.template.disabled = true;
				axis.renderer.labels.template.radius = am4core.percent(15);
				axis.renderer.labels.template.fontSize = "0.9em";

				/**
				 * Axis for ranges
				 */
				var axis2 = chart.xAxes.push(new am4charts.ValueAxis());
				axis2.min = -1;
				axis2.max = 1;
				axis2.renderer.innerRadius = 10;
				axis2.strictMinMax = true;
				axis2.renderer.labels.template.disabled = true;
				axis2.renderer.ticks.template.disabled = true;
				axis2.renderer.grid.template.disabled = false;
				axis2.renderer.grid.template.opacity = 0.5;
				axis2.renderer.labels.template.bent = true;
				axis2.renderer.labels.template.fill = am4core.color("#000");
				axis2.renderer.labels.template.fontWeight = "bold";
				axis2.renderer.labels.template.fillOpacity = 0.3;

				// Add data
				const data = {
					score: <? echo $sensor_last_reading ?>
				};

				// Ranges
				function createRange( start, end, title, color ) {
					var range = axis2.axisRanges.create();
					range.axisFill.fill = am4core.color(color);
					range.axisFill.fillOpacity = 0.8;
					range.axisFill.zIndex = -1;
					range.value = start;
					range.endValue = end;
					range.grid.strokeOpacity = 0;
					range.stroke = am4core.color(color).lighten(-0.1);
					range.label.inside = true;
					range.label.text = title.toUpperCase();
					range.label.inside = true;
					range.label.location = 0.5;
					range.label.inside = true;
					range.label.radius = am4core.percent(5);
					range.label.paddingBottom = -5; // ~half font size
					range.label.fontSize = "0.9em";
				}

				createRange( -1, -0.5, 'Low', '#ee1f25' );
				createRange( -0.5, 0.5, 'Normal', '#54b947' );
				createRange( 0.5, 1.0, 'High', '#ee1f25' );

				// Readings
				var label = chart.radarContainer.createChild(am4core.Label);
				label.isMeasured = false;
				label.fontSize = "6em";
				label.x = am4core.percent(50);
				label.paddingBottom = 15;
				label.horizontalCenter = "middle";
				label.verticalCenter = "bottom";
				label.text = data.score.toFixed(2);

				// Hand
				var hand = chart.hands.push(new am4charts.ClockHand());
				hand.axis = axis2;
				hand.innerRadius = am4core.percent(55);
				hand.startWidth = 8;
				hand.pin.disabled = true;
				hand.value = data.score;
				hand.fill = am4core.color("#444");
				hand.stroke = am4core.color("#000");

				hand.events.on("positionchanged", function(){
					label.text = axis2.positionToValue(hand.currentPosition).toFixed(2);
				})

				/*
				WebSockets
				*/
				parent.$( parent.document ).on( "sensor_reading", ( event, date, data, reading_uuid ) => {
					hand.showValue(data, 1000, am4core.ease.cubicOut);
				} );
			} );
		</script>
	</head>
	<body class="iframe-graph-body">
		<div id="chartdiv"></div>
	</body>
</html>