<?

class Configurable
{

	private $table;
	private $uuid;
	private $config;

	function __construct( $table, $uuid ) {
		$this->table = $table;
		$this->uuid = $uuid;

		$this->load();
	}

	private function load() {
		require( 'services/DatabaseConnect.php' );

		if ( $statement = $mysqli->prepare( "SELECT configuration FROM $this->table WHERE uuid=?;" ) ) {
			$statement->bind_param( 's', $this->uuid );
			$statement->execute();
			$statement->store_result();
			$statement->bind_result( $configuration );
			
			$statement->fetch();

			$configuration = json_decode( $configuration, true );

			if ( $configuration == NULL ) {
				$configuration = array();
			}

			$this->config = $configuration;
		}
	}

	public function save() {
		require( 'services/DatabaseConnect.php' );

		$configuration_json = json_encode( $this->config );

		if ( $statement = $mysqli->prepare( "UPDATE $this->table SET configuration = ? WHERE uuid = ?" ) ) {
			$statement->bind_param( 'ss', $configuration_json, $this->uuid );
			if ( !$statement->execute() ) {
				// TODO: Handle SQL error
			}
		}
	}

	public function get( $property, $default = null ) {
		$value = $this->config[ $property ];

		if ( $value == null && $default != null ) {
			$value = $default;
		}

		return $value;
	}

	public function set( $property, $value) {
		$this->config[ $property ] = $value;
	}

}

?>