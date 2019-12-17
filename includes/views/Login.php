<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | Login</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
</head>
<body>
	<div class="grid-container">
		<div class="grid-item grid-item-3x1">
			<img src="../static/img/logo.png" style="height: 80px; margin: 16px">
		</div>

		<div class="grid-item grid-item-3x1"><div>
			<form action="../includes/services/userLogin.php" method="post">
				<h1>Login</h1>
				<p>Username</p><input type="text" name="username"><br><br>
				<p>Password</p><input type="password" name="password"><br><br>
				<input type="submit" value="Login">
			</form>
		</div></div>
	</div>
</body>
</html>