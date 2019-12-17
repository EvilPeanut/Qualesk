<?

require_once( 'services/DatabaseConnect.php' );

session_start();

$username = $_POST[ 'username' ];
$password = $_POST[ 'password' ];

if ( $statement = $mysqli->prepare( "SELECT user_id, password, password_salt FROM users WHERE username = ? LIMIT 1" ) ) {
	$statement->bind_param( 's', $username );
	$statement->execute();
	$statement->store_result();
	$statement->bind_result( $db_user_id, $db_password, $db_password_salt );
	$statement->fetch();
	$password = hash( 'sha512', $password . $db_password_salt );

	if ( $db_password == $password ) {
		$_SESSION[ 'user_id'] = $db_user_id;
		$_SESSION[ 'password' ] = $db_password;

		// Insert login entry
		if ( $statement = $mysqli->prepare( "INSERT INTO logins (user_id, date) VALUES (?, ?)" ) ) {
			$date = date( 'Y-m-d H:i:s' );
			$statement->bind_param( 'ss', $_SESSION[ 'user_id' ], $date );
			$statement->execute();
		}

		header( 'Location: ' . $_SERVER[ 'HTTP_REFERER' ] );
	} else {
		// TODO: Handle incorrect password
		header( 'Location: ' . $_SERVER[ 'HTTP_REFERER' ] );
	}
}

?>