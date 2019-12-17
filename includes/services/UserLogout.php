<?

session_start();

unset( $_SESSION[ 'user_id' ] );

header( 'Location: http://' . $_SERVER[ 'HTTP_HOST' ] );

?>