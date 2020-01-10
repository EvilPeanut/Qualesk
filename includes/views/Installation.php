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
		<div style="position: absolute; top: 0; left: 0; width: 100%; height: 64px; background-color: #1976d2; box-shadow: 0 0px 10px 0 rgba(0, 0, 0, .3);">
			<a href="../"><img src="../static/img/logo.png" style="position: absolute; top: 8px; left: 1vw; height: 48px; filter: brightness(10);"></a>
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

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>