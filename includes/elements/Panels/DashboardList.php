<!-- Add prompt -->
<div id="div_overlay_add" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; background-color: rgba(0, 0, 0, 0.6); display: none">
	<div id="div_prompt" style="display: block; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 32px; background-color: white; border-radius: 16px;">
		<h1>Add Dashboard</h1>

		<form id="form_create" action="../includes/services/dashboardCreate.php" method="post">
			<p>Name</p><input type="text" name="name" style="width: 256px"><br><br>
			<p>Description</p><input type="text" name="description" style="width: 256px"><br><br>
			<div onclick="$( '#form_create' ).submit()" class="inline-button"><p>Create</p></div>
			<div onclick="$( '#div_overlay_add' ).hide()" class="inline-button"><p>Close</p></div>
		</form>
	</div>
</div>
<!-- Add prompt -->

<div class="grid-item"><div>
	<h1 style="display: inline">Dashboards <span style="color: grey; font-size: small"><? echo DashboardManager::get_count(); ?> Total</span></h1>
	<img style="display: inline; float: right; cursor: pointer" src="../static/img/icon_add.png" onclick="$( '#div_overlay_add' ).show()"/>
	<div style="height: 512px; margin-top: 16px; overflow-y: auto">
		<? DashboardManager::print_list( true ); ?>
	</div>
</div></div>