<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | Dashboard</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>
		<? include 'elements/prompt.php'; ?>

		<?

		include 'elements/panels/systemsMap.php';
		include 'elements/panels/systemsList.php';

		?>
		
		<div class="grid-item grid-item-2x1"><? include 'elements/panels/eventsLog.php'; ?></div>
		
		<?

		include 'elements/panels/dashboardList.php';
		
		if ( AccountManager::has_permission( 'admin_features' ) ) {
			include 'elements/panels/adminLinks.php';
		}

		include 'elements/bottomBar.php'; 

		?>
	</div>
</body>
</html>