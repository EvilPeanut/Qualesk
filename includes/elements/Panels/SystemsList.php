<div class="grid-item"><div>
	<h1>Systems <span style="color: grey; font-size: small"><? echo SystemManager::get_count(); ?> Total</span></h1>
	<div style="height: 512px; overflow-y: auto">
		<? SystemManager::print_system_list(); ?>
	</div>
</div></div>