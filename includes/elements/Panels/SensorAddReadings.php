<div class="grid-item"><div>
	<h1>Import Sensor Readings</h1>
	<form action="../includes/services/sensorReadingImport.php" method="post" enctype="multipart/form-data">
		<input type="file" name="CSVfile" id="CSVfile">
		<br><br>
		<p>Sensor Name</p><input type="text" name="sensor_name" value="<? echo $sensor['name']; ?>"><br><br>
		<input type="submit" value="Import Sensor Readings">
	</form>
	<br>
	<h1>Add Sensor Reading</h1>
	<form action="../includes/services/sensorReadingCreate.php" method="post">
		<p>Date</p><input type="text" name="date"><br><br>
		<p>Data</p><input type="text" name="data"><br><br>
		<input type="submit" value="Create Sensor Reading">
	</form>
</div></div>