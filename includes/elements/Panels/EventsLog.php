<script>
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
				user_uuid: '<? echo AccountManager::get_user_uuid() ?>'
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

		if ( json.type === 'log_entry' ) {
			var icon = '../static/img/info.png';
			var style = '';

			if ( json.severity == 1 ) {
				icon = '../static/img/warning.png';
				style = 'background-color: rgba(255, 127, 0, 0.2)';
			} else if ( json.severity == 2 ) {
				icon = '../static/img/urgent.png';
				style = 'background-color: rgba(255, 0, 0, 0.2)';
			}

			$( "#div_log" ).prepend( "<div style='" + style + "' severity='" + json.severity + "'><img style='padding: 4px 0px 0px 4px' src='" + icon + "'><p style='display: inline'><span style='padding: 0px 8px'>" + json.date.slice(0, -4) + "</span><a href='../sensor/" + json.sensor_uuid + "'>" + json.message + "</a></p></div>" );

			refresh_log_filter();
		}
	}
}

connect();

// Fix div_log height
$( document ).ready( () => {
	$( "#div_log" ).height( $( "#div_log" ).parent().height() - 64 );
} );
</script>

<div style="height: calc( 100% - 32px )">
	<h1>Log</h1>
	<span style="letter-spacing: 1px">
		<input type="checkbox" id="checkbox-log-info" checked><p style="display: inline">Info</p>
		<input type="checkbox" id="checkbox-log-warning" checked><p style="display: inline">Warning</p>
		<input type="checkbox" id="checkbox-log-urgent" checked><p style="display: inline">Urgent</p>
	</span>
	<script>
		$( "#checkbox-log-info" ).change( refresh_log_filter );
		$( "#checkbox-log-warning" ).change( refresh_log_filter );
		$( "#checkbox-log-urgent" ).change( refresh_log_filter );

		function refresh_log_filter() {
			$( "#div_log > div" ).each( function() {
				if ( $( this ).attr( "severity" ) == 0 ) {
					$( this ).css( "display", $( "#checkbox-log-info" ).prop( "checked" ) ? "block" : "none" );
				} else if ( $( this ).attr( "severity" ) == 1 ) {
					$( this ).css( "display", $( "#checkbox-log-warning" ).prop( "checked" ) ? "block" : "none" );
				} else {
					$( this ).css( "display", $( "#checkbox-log-urgent" ).prop( "checked" ) ? "block" : "none" );
				}
			} );
		}
	</script>
	<div id="div_log" style="height: 280px; margin-top: 8px; overflow-y: auto">
		<?

		if ( isset( $system_uuid ) ) {
			LogManager::print_log( $system_uuid );
		} else if ( isset( $sensor_array_uuid ) ) {
			LogManager::print_log( NULL, $sensor_array_uuid );
		} else {
			LogManager::print_log();
		}

		?>
	</div>
</div>