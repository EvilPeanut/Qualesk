<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | Login</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="stylesheet" type="text/css" href="../static/css/view/login.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
	<script>
		function login() {
			$.ajax({
				method: "POST",
				url: "includes/services/userLogin.php",
				data: { 
					username: $( "input[name='username']" ).val(),
					password: $( "input[name='password']" ).val()
				}
			})
			.done(function( msg ) {
				if ( msg == true ) {
					location.reload();
				} else {
					$( "#text_nologin" ).show();
				}
			});
		}
	</script>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>

		<div class="grid-item grid-item-3x1"><div>
			<h1>Login</h1>
			<p id="text_nologin">Incorrect credentials</p>
			<p>Username</p><input type="text" name="username"><br><br>
			<p>Password</p><input type="password" name="password"><br><br>
			<input onclick="login()" type="button" value="Login">
		</div></div>

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>