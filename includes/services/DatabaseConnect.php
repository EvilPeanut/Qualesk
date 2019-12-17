<?

require_once( 'classes/config.php' );

Config::load();

mysqli_report( MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX );

$mysqli = new mysqli( 'p:' . Config::get( 'database_host' ) , Config::get( 'database_username' ), Config::get( 'database_password' ), Config::get( 'database_name' ) );

?>