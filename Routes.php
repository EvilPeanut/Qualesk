<?

session_start();

require_once( 'classes/accountManager.php' );

Route::set( '/^$/' , function() {
	if ( Config::get( 'installed' ) == 1 ) {
		if ( AccountManager::is_logged_in() ) {
			Dashboard::CreateView( 'IndexDashboard' );
			return;
		} else {
			Login::CreateView( 'Login' );
			return;
		}
	} else {
		Installation::CreateView( 'Installation' );
		exit;
	}
});

Route::set( '/(gauge\/.*)/' , function() {
	Generic::CreateView( 'Gauge' );
	exit;
});

Route::set( '/(compound\/.*)/' , function() {
	Generic::CreateView( 'CompoundGraph' );
	exit;
});

Route::set( '/(graph\/.*)/' , function() {
	Generic::CreateView( 'SensorGraph' );
	exit;
});

if ( AccountManager::is_logged_in() ) {
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

	Route::set( '/(collective\/.*)/' , function() {
		Generic::CreateView( 'CompoundGraphOverview' );
	});

	Route::set( '/(sensor\/.*)/' , function() {
		Generic::CreateView( 'SensorOverview' );
	});

	Route::set( '/(dashboard\/.*)/' , function() {
		Generic::CreateView( 'Dashboard' );
	});

	Route::set( '/^noperm$/' , function() {
		Generic::CreateView( 'PermissionDenied' );
	});
} else {
	Login::CreateView( 'Login' );
}

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