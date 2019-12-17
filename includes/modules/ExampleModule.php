<?

ModuleManager::register_module( new class extends Module {

	function __construct() {
		
	}

	function get_name() {
		return "Example Module";
	}

	function on_page_load( $page_uri ) {

	}

} );

?>