<?

class Admin extends Controller {

	public static function Init() {

	}

	public static function get_count() {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT COUNT(*) FROM users;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $count );

			$statement->fetch();

			return $count;
		}
	}

	public static function get_login_count() {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT COUNT(*) FROM logins;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $count );

			$statement->fetch();

			return $count;
		}
	}

	public static function PrintUserList() {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT user_id, username FROM users;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $user_id, $username );
			
			while ( $statement->fetch() ) {
				echo '<img src="../static/img/remove.png" style="cursor: pointer" onclick="show_prompt(\'Remove user\', \'Are you sure you want to remove this user?\', \'../includes/services/userRemove.php?uuid=' . $user_id . '\')"><p style="display: inline-block; margin: 0px 0px 8px 8px"> ' . $username . '</p><br>';
			}
		}
	}

	public static function PrintRecentLogins() {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT username, date FROM logins JOIN users ON logins.user_id=users.user_id ORDER BY date DESC;" ) ) {
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $username, $date );
			
			while ( $statement->fetch() ) {
				echo '<p>' . $username . ' at ' . $date . '</p>';
			}
		}
	}
	
}

?>