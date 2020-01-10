<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | Login</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
</head>
<body>
	<div class="grid-container">
		<div style="position: absolute; top: 0; left: 0; width: 100%; height: 64px; background-color: #1976d2; box-shadow: 0 0px 10px 0 rgba(0, 0, 0, .3);">
			<a href="../"><img src="../static/img/logo.png" style="position: absolute; top: 8px; left: 1vw; height: 48px; filter: brightness(10);"></a>

			<p style="position: absolute; left: calc(2vw + 188px); bottom: 10px; color: antiquewhite"><? echo Config::get('project_name'); ?></p>
		</div>

		<div class="grid-item grid-item-3x1"><div>
			<form action="../includes/services/userLogin.php" method="post">
				<h1>Login</h1>
				<p>Username</p><input type="text" name="username"><br><br>
				<p>Password</p><input type="password" name="password"><br><br>
				<input type="submit" value="Login">
			</form>
		</div></div>

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>