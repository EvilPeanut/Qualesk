<script>
	/*
		WebSockets
	*/
	var server_address = '<? echo Config::get( 'wsserver_host' ); ?>';

	function connect() {
		// Store and open the server websocket connection
		parent.connection = parent.connection ? parent.connection : new WebSocket( server_address );

		// When the connection is opened
		parent.connection.onopen = () => {
			parent.connection.send( JSON.stringify( {
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
		parent.connection.onclose = () => {
			setTimeout( () => {
				parent.connection = null;
				connect();
			}, 1000);
		};

		// When data is received
		const originalMethod = parent.connection.onmessage;

		parent.connection.onmessage = ( message ) => {
			if ( originalMethod ) originalMethod( message );
			var json = JSON.parse( message.data );

			if ( json.type === 'sensor_reading' ) {
				$( document ).trigger( "sensor_reading", [ json.date, json.data, json.reading_uuid, json.sensor_uuid ] );
			}
		}
		
	}

	connect();
</script>