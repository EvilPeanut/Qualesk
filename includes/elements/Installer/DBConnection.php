<script>
	function connection_test() {
		$.ajax({
			method: "POST",
			url: "includes/services/ajaxDBConnectionTest.php",
			data: { host: $('input[name="database_host"]').val(), database: $('input[name="database_name"]').val(), username: $('input[name="database_username"]').val(), password: $('input[name="database_password"]').val() }
		})
		.done(function( msg ) {
			if ( msg == 0 ) {
				// Database connection error
				$("#div_errors").html( "<img src='../static/img/urgent.png'><p style='display: inline'> Database connection failed. Are the details correct?</p><br><br>" );
			} else if ( msg == 1 ) {
				// Database connection success
				$("#form").submit();
			}
		});
	}
</script>

<div>
	<h1>Database Connection (Step 2 / 5)</h1>
	<p>We need to connect to a database to store our data</p><br>
	<img src="../static/img/warning.png"><p style="display: inline"> You need to have MySQL installated on the host with an existing database. This database can be empty or contain existing data</p><br><br>

	<div id="div_errors"></div>

	<form action="../includes/services/install.php" method="post" id="form">
		<p>Host <span style="color: grey; font-size: small">eg. localhost</span></p><input type="text" name="database_host"><br><br>
		<p>Database Name <span style="color: grey; font-size: small">eg. weatherstation</span></p><input type="text" name="database_name"><br><br>
		<p>Username</p><input type="text" name="database_username"><br><br>
		<p>Password</p><input type="password" name="database_password"><br><br>
		<input onclick="connection_test()" type="button" value="Next">
	</form>
</div>