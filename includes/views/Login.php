<!DOCTYPE html>
<html>
<head>
	<title><? echo Config::get('site_name'); ?> | Login</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
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
		<div style="position: absolute; top: 0; left: 0; width: 100%; height: 64px; background-color: #1976d2; box-shadow: 0 0px 10px 0 rgba(0, 0, 0, .3);">
			<a href="../"><img src="../static/img/logo.png" style="position: absolute; top: 8px; left: 1vw; height: 48px"></a>

			<p style="position: absolute; left: calc(2vw + 188px); bottom: 10px; color: antiquewhite"><? echo Config::get('project_name'); ?></p>
		</div>

		<div class="grid-item grid-item-3x1"><div>
			<h1>Login</h1>
			<p id="text_nologin" style="margin-bottom: 16px; background-color: #ff6961; color: white; width: fit-content; padding: 8px 16px; border-radius: 4px; display: none">Incorrect credentials</p>
			<p>Username</p><input type="text" name="username"><br><br>
			<p>Password</p><input type="password" name="password"><br><br>
			<input onclick="login()" type="button" value="Login">
		</div></div>

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>