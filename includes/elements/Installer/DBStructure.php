<script>
	$.ajax( {
		method: "POST",
		url: "includes/services/ajaxDBStructureTest.php",
	} )
	.done( ( msg ) => {
		if ( msg.includes( "Complete" ) ) {
			window.location.replace( "../includes/services/install.php" );
		} else {
			$( "#div_log" ).append( "<p>" + msg + "</p>" );
		}
	} );
</script>

<div>
	<h1>Database Structure</h1>

	<p>Please wait whilst we verify the database structure...</p><br>

	<div id="div_log"></div><br>
</div>