<script>
	/*
		WebSockets
	*/
	var server_address = '<? echo Config::get( 'wsserver_host' ); ?>';
	var connection;

	function connect() {
		// Store and open the server websocket connection
		connection = new WebSocket( server_address );

		// When the connection is opened
		connection.onopen = () => {
			connection.send( JSON.stringify( {
				type: 'user_definition',
				data: {
					core_auth_key: '<? echo Config::get( 'wsserver_auth_key' ); ?>',
					user_uuid: '<? echo AccountManager::get_user_uuid() ?>',
					current_view: 'sensor',
					current_view_uuid: '<? echo $sensor_uuid; ?>'
				}
			} ) );
		};

		// Attempt reconnection every second
		connection.onclose = () => {
			setTimeout( () => {
				connect();
			}, 1000);
		};

		// When data is received
		connection.onmessage = ( message ) => {
		    var json = JSON.parse( message.data );

			if ( json.type === 'sensor_reading' ) {
				$( document ).trigger( "sensor_reading", [ json.date, json.data ] );
			}
		}
	}

	connect();
</script>