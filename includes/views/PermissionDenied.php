<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | Permission Denied</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>

		<div class="grid-item grid-item-3x1"><div>
			<h1>Permission Denied</h1>
			<p>You don't have permission to access this page</p>
		</div></div>

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>