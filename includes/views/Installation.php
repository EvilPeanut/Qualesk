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
		<? include 'elements/topBar.php'; ?>

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