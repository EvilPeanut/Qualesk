<?

require_once( 'classes/accountManager.php' );
AccountManager::remove_account( $_GET["uuid"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>