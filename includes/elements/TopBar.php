<div style="position: absolute; top: 0; left: 0; width: 100%; height: 64px; background-color: #1976d2; box-shadow: 0 0px 10px 0 rgba(0, 0, 0, .3)">
	<a href="../"><img src="../static/img/logo.png" style="position: absolute; top: 8px; left: 1vw; height: 48px"></a>

	<p style="position: absolute; left: calc(2vw + 188px); bottom: 10px; color: white; opacity: 0.75"><? echo Config::get('project_name'); ?></p>

	<a href="../includes/services/userLogout.php" <? echo AccountManager::is_logged_in() ? '' : 'hidden' ?>>
		<div style="position: absolute; bottom: 0px; right: 1vw; background-color: rgba(255, 255, 255, 0.15); padding: 8px 16px; border-radius: 4px 4px 0px 0px"><p style="color: white">Log Out</p></div>
	</a>
</div>