<?

session_start();

Route::set( '/^$/' , function() {
	if ( Config::get( 'installed' ) == 1 ) {
		require_once( 'classes/accountManager.php' );
		if ( AccountManager::is_logged_in() ) {
			Dashboard::CreateView( 'IndexDashboard' );
		} else {
			Login::CreateView( 'Login' );
		}
	} else {
		Installation::CreateView( 'Installation' );
	}
});

Route::set( '/^admin\/modules$/' , function() {
	Admin::CreateView( 'Modules' );
});

Route::set( '/^admin\/users$/' , function() {
	Admin::CreateView( 'UserManagement' );
});

Route::set( '/^admin\/systems$/' , function() {
	Admin::CreateView( 'SystemManagement' );
});

Route::set( '/^admin\/arrays$/' , function() {
	Admin::CreateView( 'SensorArrayManagement' );
});

Route::set( '/^admin\/array-import$/' , function() {
	Admin::CreateView( 'SensorArrayImport' );
});

Route::set( '/^admin\/hydrovu-import$/' , function() {
	Admin::CreateView( 'HydroVuImport' );
});

Route::set( '/^admin\/sensors$/' , function() {
	Admin::CreateView( 'SensorManagement' );
});

Route::set( '/(system\/.*)/' , function() {
	Generic::CreateView( 'SystemOverview' );
});

Route::set( '/(array\/.*)/' , function() {
	Generic::CreateView( 'SensorArrayOverview' );
});

Route::set( '/(sensor\/.*)/' , function() {
	Generic::CreateView( 'SensorOverview' );
});

Route::set( '/(graph\/.*)/' , function() {
	Generic::CreateView( 'SensorGraph' );
});

Route::set( '/(dashboard\/.*)/' , function() {
	Generic::CreateView( 'Dashboard' );
});

Route::set( '/^noperm$/' , function() {
	Generic::CreateView( 'NoPermissionNotice' );
});

/*
How to set for http://localhost/test:
Route::set( '/^test$/' , function() {
	Test::CreateView( 'Test' );
});

How to set for http://localhost/test or http://localhost/test/
Route::set( '/(^test$|test\/.*)/' , function() {
	Test::CreateView( 'Test' );
});*/

?>