<?

class Controller {

	public static function CreateView( $viewName ) {
		require_once( "views/${viewName}.php" );
		static::Init();
	}

	public static function Init() {}
	
}

?>