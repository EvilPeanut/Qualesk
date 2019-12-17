<?

require_once( 'classes/config.php' );

Config::load();

if ( Config::get( 'installation_step' ) == null ) {
	Config::set( 'installation_step' , 'basic' );
} else if ( Config::get( 'installation_step' ) == 'basic' ) {
	Config::set( 'site_name' , $_POST["site_name"] );
	Config::set( 'project_name' , $_POST["project_name"] );
	Config::set( 'installation_step' , 'dbconnection' );
} else if ( Config::get( 'installation_step' ) == 'dbconnection' ) {
	Config::set( 'database_host' , $_POST["database_host"] );
	Config::set( 'database_name' , $_POST["database_name"] );
	Config::set( 'database_username' , $_POST["database_username"] );
	Config::set( 'database_password' , $_POST["database_password"] );
	Config::set( 'installation_step' , 'dbstructure' );
} else if ( Config::get( 'installation_step' ) == 'dbstructure' ) {
	Config::set( 'installation_step' , 'admin' );
} else if ( Config::get( 'installation_step' ) == 'admin' ) {
	require_once( 'classes/accountManager.php' );
	AccountManager::create_account( '' , $_POST["admin_username"], $_POST["admin_password"], true );
	Config::set( 'installation_step' , 'wsserver' );
} else if ( Config::get( 'installation_step' ) == 'wsserver' ) {
	Config::set( 'wsserver_host' , $_POST["wsserver_host"] );
	Config::set( 'wsserver_auth_key' , $_POST["wsserver_auth_key"] );
	Config::set( 'installation_step' , 'complete' );
} else if ( Config::get( 'installation_step' ) == 'complete' ) {
	Config::set( 'installed' , 1 );
}

Config::save();

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>