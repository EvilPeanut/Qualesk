<?

if ( !AccountManager::has_permission( 'admin_features' ) ) {
	header('Location: ../noperm');
}

?>
<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | System Management</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>
		<? include 'elements/prompt.php'; ?>

		<div class="grid-item"><div>
			<h1>Systems <span style="color: grey; font-size: small"><? echo SystemManager::get_count(); ?> total</span></h1>
			<? SystemManager::print_system_list( true ); ?>
		</div></div>

		<div class="grid-item"><div>
			<h1>Add New System</h1>
			<form action="../includes/services/systemCreate.php" method="post">
				<p>Name</p><input type="text" name="name"><br><br>
				<p>Description</p><input type="text" name="description"><br><br>
				<input type="submit" value="Create System">
			</form>
		</div></div>

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>