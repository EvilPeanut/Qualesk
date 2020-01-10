<?

class Config
{

	private static $config_path = 'C:\qualesk_config.php';
	private static $config = array();

	public static function load() {
		if ( file_exists( self::$config_path ) ) {
			self::$config = include self::$config_path;
		} else {
			$str_config = var_export( array() , true );
			file_put_contents( self::$config_path , "<?php return $str_config ;");
		}
	}

	public static function get( $property ) {
		if ( array_key_exists( $property, self::$config ) ) {
			return self::$config[ $property ];
		} else {
			return null;
		}
	}

	public static function set( $property, $value ) {
		self::$config[ $property ] = $value;
	}

	public static function save()
	{
		$str_config = var_export( self::$config, true );
		file_put_contents( self::$config_path , "<?php return $str_config ;" );
	}

}

?>