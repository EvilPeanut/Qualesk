<?

if ( !AccountManager::has_permission( 'admin_features' ) ) {
	header('Location: ../noperm');
}

move_uploaded_file( $_FILES[ "CSVfile" ][ "tmp_name" ], sys_get_temp_dir() . $_FILES[ "CSVfile" ][ "name" ] );

?>
<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | Array Import</title>
	<link rel="stylesheet" type="text/css" href="../../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../../static/img/favicon.png" />
	<script src="../../static/js/jquery-3.4.0.min.js"></script>
	<script>
		$(document).ready(function(){
			var responseLength = false;

			$.ajax({
				method: "POST",
				url: "../includes/services/sensorArrayImport.php?file=<? print $_FILES[ "CSVfile" ][ "name" ]; ?>&sensor_array=<? print $_POST[ "sensor_array" ] ?>",
				xhrFields: {
					onprogress: function( e ) {
						var thisResponse, response = e.currentTarget.response;

						if ( responseLength === false ) {
							thisResponse = response;
							responseLength = response.length;
						} else {
							thisResponse = response.substring( responseLength );
							responseLength = response.length;
						}

						response = JSON.parse( "{" + thisResponse.split('{')[1] );

						if ( response.stage == "sensor_create" ) {
							$( "#sensor_create_pb" ).attr( "max", response.total );
							$( "#sensor_create_pb" ).attr( "value", response.current );
						} else {
							$( "#sensor_create_pb" ).attr( "value", $( "#sensor_create_pb" ).attr( "max" ) );

							$( "#data_create_pb" ).attr( "max", response.total );
							$( "#data_create_pb" ).attr( "value", response.current );
						}
					}
				},
				success: function( data ) {
					window.history.back();
				}
			});
		});
	</script>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>

		<div class="grid-item grid-item-3x1"><div>
			<h1>Import Sensor Array Sensors</h1>

			<p>Sensor Array Creation</p>
			<progress value="0" id="sensor_create_pb" style="width: 100%"></progress>

			<br><br>

			<p>Data Point Creation</p>
			<progress value="0" id="data_create_pb" style="width: 100%"></progress>
		</div></div>

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>