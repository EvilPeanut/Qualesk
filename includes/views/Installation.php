<!DOCTYPE html>
<html>
<head>
	<title>Qualesk</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
</head>
<body>
	<div class="grid-container">
		<div class="grid-item grid-item-3x1">
			<img src="../static/img/logo.png" style="height: 80px; margin: 16px">
			<p style="float: right; margin: 16px; color: lightgrey">v0.0.1</p>
		</div>

		<div class="grid-item grid-item-3x1">
			<?
			if ( Config::get( 'installation_step' ) == null ) {
				include 'elements/installer/overview.php';
			} else {
				include 'elements/installer/' . Config::get( 'installation_step' ) . '.php';
			}
			?>
		</div>
	</div>
</body>
</html>