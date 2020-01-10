<?

if ( !AccountManager::has_permission( 'admin_features' ) ) {
	header('Location: ../noperm');
}

?>
<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | User Management</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>
		<? include 'elements/prompt.php'; ?>

		<div class="grid-item"><div>
			<h1>Users <span style="color: grey; font-size: small"><? echo Admin::get_count(); ?> Total</span></h1>
			<? Admin::PrintUserList(); ?>
		</div></div>

		<div class="grid-item"><div>
			<h1>Add New User</h1>
			<form action="../includes/services/userCreate.php" method="post">
				<p>Username</p><input type="text" name="username"><br><br>
				<p>Password</p><input type="password" name="password"><br><br>
				<input type="submit" value="Create User">
			</form>
		</div></div>

		<div class="grid-item"><div>
			<h1>Recent Logins <span style="color: grey; font-size: small"><? echo Admin::get_login_count(); ?> Total</span></h1>
			<? Admin::PrintRecentLogins(); ?>
		</div></div>

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>