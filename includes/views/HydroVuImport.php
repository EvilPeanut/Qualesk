<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | HydroVu Import</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
	<script>
		$(document).ready(function(){
			var responseLength = false;

			$.ajax({
				method: "POST",
				url: "../includes/services/hydroVuImport.php?client_id=<? print $_POST[ "client_id" ] ?>&client_secret=<? print $_POST[ "client_secret" ] ?>",
				xhrFields: {
					onprogress: function( msg ) {
						var thisResponse, response = msg.currentTarget.response;

						if ( responseLength === false ) {
							thisResponse = response;
							responseLength = response.length;
						} else {
							thisResponse = response.substring( responseLength );
							responseLength = response.length;
						}

						$( "#status" ).text( thisResponse );
					}
				},
				success: function( data ) {
					//window.history.back();
				}
			});
		});
	</script>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>
		
		<div class="grid-item grid-item-3x1"><div>
			<h1>Importing HydroVu Data</h1>
			<p id="status"></p>
		</div></div>

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>