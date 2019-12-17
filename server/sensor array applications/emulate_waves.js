process.title = 'Wave Sensor Array Emulation';

//TODO: Add automatic reconnect in event of connection failure (include buffer so data isn't lost)

//
// Sensor array definition and properties
//
var sensor_array_definition = {
	core_address: 'ws://localhost:1337',
	core_auth_key: 'eeac16b2-4fed-4f36-93a1-f367982843a8',

	sensor_array: {
		// TODO: Automatic sensor_array uuid assignment
		uuid: 'a1ca1cd5-fa07-48d1-9558-beb6ef90073d',
		name: 'Sutton Harbor',
		description: 'An emulated array of wave sensors at Sutton Harbor',
		system_uuid: '39a1c676-2d6f-4fe9-8c99-b071c71d4293',
		latitude: 50.36630957809058,
		longitude: -4.133916201984903
	},

	//TODO: Some UUID magic? get_uuid('temperature_sensor')?
	sensors: {
		'aec6ee61-685a-4414-bcc6-93ab43d60b32': {
			name: 'Sine Wave Sensor',
			description: 'A fake sensor which outputs a continuous sine wave',
			type: {
				uuid: '617e8c3f-eefb-4ef9-b3fa-a608ec883db3',
				name: 'Wave',
				description: 'Wave sensor type using the double data type',
				unit: '',
				data_type: 'DOUBLE'
			}
		},
		'aec6ee61-685a-4414-bcc6-93ab43d60b31': {
			name: 'Cosine Wave Sensor',
			description: 'A fake sensor which outputs a continuous cosine wave',
			type: {
				uuid: '617e8c3f-eefb-4ef9-b3fa-a608ec883db3',
			}
		},
		'aec6ee61-685a-4414-bcc6-93ab43d60b30': {
			name: 'Tangent Wave Sensor',
			description: 'A fake sensor which outputs a continuous tangent wave',
			type: {
				uuid: '617e8c3f-eefb-4ef9-b3fa-a608ec883db3',
			}
		}
	}
}

//
// Connect to the Qualesk data and processing server
//
var webSocketClient = require( 'websocket' ).client;
var client = new webSocketClient();
var connection;

client.connect( sensor_array_definition.core_address );

client.on( 'connect', ( server_connection ) => {
	console.error( getTime(), 'Connected to Qualesk data and processing server at', sensor_array_definition.core_address );

	connection = server_connection;

	// Send sensor definition
	connection.sendUTF( JSON.stringify( {
		type: 'sensor_array_definition',
		data: sensor_array_definition
	} ) );
} );

client.on( 'connectFailed', () => {
	console.error( getTime(), 'Failed to connect to Qualesk data and processing server at', sensor_array_definition.core_address );
} );

setInterval( () => {
	if ( connection && connection.connected ) {
		connection.sendUTF( JSON.stringify( {
			type: 'sensor_reading',
			sensor_uuid: 'aec6ee61-685a-4414-bcc6-93ab43d60b32',
			date: new Date(),
			data: Math.sin( Date.now() )
		} ) );

		connection.sendUTF( JSON.stringify( {
			type: 'sensor_reading',
			sensor_uuid: 'aec6ee61-685a-4414-bcc6-93ab43d60b31',
			date: new Date(),
			data: Math.cos( Date.now() )
		} ) );

		connection.sendUTF( JSON.stringify( {
			type: 'sensor_reading',
			sensor_uuid: 'aec6ee61-685a-4414-bcc6-93ab43d60b30',
			date: new Date(),
			data: Math.tan( Date.now() )
		} ) );
	}
}, 500);

//
// Return time in human format ([hh:mm:ss])
//
function getTime() {
	var date = new Date();
	var hour = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
	var min = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
	var sec = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();
	return( '[' + hour + ':' + min + ':' + sec + ']' );
}