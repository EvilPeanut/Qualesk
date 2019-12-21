<div class="grid-item"><div>
	<h1>Sensor Reading Boundaries</h1>
	<form action="../includes/services/sensorBoundariesSet.php" method="post">
		<p>Upper Urgent Boundary</p><input type="text" name="upper_urgent_boundary" value="<? echo $upper_urgent_boundary; ?>"><br><br>
		<p>Upper Warning Boundary</p><input type="text" name="upper_warning_boundary" value="<? echo $upper_warning_boundary; ?>"><br><br>
		<p>Lower Warning Boundary</p><input type="text" name="lower_warning_boundary" value="<? echo $lower_warning_boundary; ?>"><br><br>
		<p>Lower Urgent Boundary</p><input type="text" name="lower_urgent_boundary" value="<? echo $lower_urgent_boundary; ?>"><br><br>
		<input type="submit" value="Set Boundaries">
	</form>
</div></div>