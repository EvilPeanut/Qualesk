<?

class ModuleManager
{

	private static $modules = [];

	public static function load() {
		foreach (glob("includes/modules/*.php") as $filename)
		{
			require_once $filename;
		}

		foreach (self::get_list() as $module) {
			$module->on_page_load( $_SERVER['REQUEST_URI'] );
		}
	}

	public static function register_module( $module ) {
		array_push( self::$modules, $module );
	}

	public static function get_list() {
		return self::$modules;
	}

	public static function get_count() {
		return count( self::$modules );
	}

}

?>