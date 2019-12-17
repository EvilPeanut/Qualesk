<script>
	$.ajax({
		method: "POST",
		url: "includes/services/ajaxDBStructureTest.php",
	})
	.done(function( msg ) {
		$("#div_log").append( "<p>" + msg + "</p>" );
	});
</script>

<div>
	<h1>Database Structure (Step 3 / 5)</h1>
	<p>Please wait whilst we verify the database structure...</p><br>

	<div id="div_log"></div><br>

	<a href="../includes/services/install.php"><button>Next</button></a>
</div>