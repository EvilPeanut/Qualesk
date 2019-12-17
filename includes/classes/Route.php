<?

class Route {
	
	public static $validRoutes = array();

	public static function set( $route, $function ) {

		self::$validRoutes[] = $route;

		if ( $route[ 0 ] == '/' && ( bool ) preg_match( $route, $_GET[ 'url' ] ) ) {
			$function->__invoke();
		} else if ( $_GET[ 'url' ] == $route ) {
			$function->__invoke();
		}
	}
	
}

?>