<?

require_once( 'services/uuid.php' );

class AccountManager
{

	public static function create_account( $email, $username, $password, $admin = false ) {
		require_once( 'services/DatabaseConnect.php' );

		// Generate user uuid
		$user_uuid = guidv4();

		// Hash password
		$password_salt = hash( 'sha512' , uniqid( openssl_random_pseudo_bytes( 16 ), TRUE ) );
		$password = hash( 'sha512' , $password . $password_salt );

		// Insert to database
		if ( $statement = $mysqli->prepare( "INSERT INTO users (user_id, username, email, password, password_salt) VALUES (?, ?, ?, ?, ?)" ) ) {
			$statement->bind_param( 'sssss', $user_uuid, $username, $email, $password, $password_salt );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}

		if ( $statement = $mysqli->prepare( "INSERT INTO user_permissions (user_id, admin_features) VALUES (?, ?)" ) ) {
			$admin = (int)$admin;
			$statement->bind_param( 'ss', $user_uuid, $admin );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}
	}

	public static function get_user_uuid() {
		return $_SESSION[ 'user_id' ];
	}

	public static function is_logged_in() {
		return isset( $_SESSION[ 'user_id' ] );
	}

	public static function has_permission( $permission ) {
		if ( !self::is_logged_in() ) {
			return false;
		}

		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT " . $permission . " FROM user_permissions WHERE user_id='" . self::get_user_uuid() . "';" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $result );
			$statement->fetch();

			return $result;
		}
	}

	public static function remove_account( $user_uuid ) {
		require_once( 'services/DatabaseConnect.php' );

		// Remove database records
		if ( $statement = $mysqli->prepare( "DELETE FROM users WHERE user_id=?" ) ) {
			$statement->bind_param( 's', $user_uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}

		if ( $statement = $mysqli->prepare( "DELETE FROM user_permissions WHERE user_id=?" ) ) {
			$statement->bind_param( 's', $user_uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}

		if ( $statement = $mysqli->prepare( "DELETE FROM logins WHERE user_id=?" ) ) {
			$statement->bind_param( 's', $user_uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}
	}

}

?>