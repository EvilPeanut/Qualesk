<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | Modules</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>

		<div class="grid-item"><div>
			<h1>Modules <span class="sml-grey"><? echo ModuleManager::get_count(); ?> Total</span></h1>
			<div class="overflow-list-512">
			<?

			foreach ( ModuleManager::get_list() as $module ) {
				echo '<p>' . $module->get_name() . '</p>';
			}

			?>
			</div>
		</div></div>

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>