<?

require_once( 'classes/accountManager.php' );
AccountManager::create_account( '' , $_POST["username"], $_POST["password"] );

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>