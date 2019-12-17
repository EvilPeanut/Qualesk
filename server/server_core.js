process.title = 'Qualesk Data and Processing Server';

//
// Server properties
//
var server_port = 1337;
var authentication_key = 'eeac16b2-4fed-4f36-93a1-f367982843a8';

//
// Module system
//
const fs = require( 'fs' );

var core = {};
core.modules = [];

fs.readdir( 'qualesk_modules', ( err, files ) => {
	if ( err ) {
		return console.log( 'Unable to find modules: ' + err );
	}

	files.forEach( ( file ) => {
		core.modules[ file ] = require( './qualesk_modules/' + file );
	} );
});

core.broadcast_event = function( event, ...arguments ) {
	for ( var module_file in core.modules ) {
		var _module = core.modules[ module_file ];

		if ( _module[ event ] ) {
			_module[ event ]( core, arguments );
		}
	}
}

//
// Config
//
core.config = require( './config' );

//
// MySQL
//
var mysql = require( 'mysql' );

core.mysql_connection = mysql.createConnection( {
	host: core.config.mysql.host,
	user: core.config.mysql.user,
	password: core.config.mysql.password
} );

core.mysql_connection.connect( ( err ) => {
	if ( err ) {
		throw err;
	}

	console.log( core.getTime(), "Connected to MySQL server" );

	accept_connections();
});

//
// Server
//
core.uuidv4 = require( 'uuid/v4' )
var webSocketServer = require( 'websocket' ).server;
var http = require( 'http' );

core.server = http.createServer();

core.server.listen( server_port, () => {
	console.log( core.getTime(), 'Server listening on port', server_port );
});

var wsServer = new webSocketServer( {
	httpServer: core.server
} );

core.user_connections = [];
core.sensor_connections = [];

var colors = require('colors');

function accept_connections() {
	wsServer.on( 'request', ( request ) => {
		var client_connection = request.accept( null, request.origin );

		client_connection.on( 'close', () => {
			if ( 'sensor_array' in client_connection.definition ) {
				console.log( core.getTime(), client_connection.definition.sensor_array.name, 'sensor array disconnected' );

				delete core.sensor_connections[ client_connection.definition.sensor_array.uuid ];
			} else {
				console.log( core.getTime(), client_connection.definition.user_uuid, 'user disconnected' );

				delete core.user_connections[ client_connection.definition.user_uuid ];
			}
		});

		client_connection.on( 'message', ( message ) => {
			try {
				var json = JSON.parse( message.utf8Data );

				if ( json.type === 'sensor_array_definition' ) {
					var data = json.data;

					core.broadcast_event( 'onSensorArrayDefinition', data );

					console.log( core.getTime(), 'Received sensor array definition from', data.sensor_array.name, '-', data.sensor_array.description );

					if ( data.core_auth_key === authentication_key ) {
						console.log( core.getTime(), data.sensor_array.name, 'sensor array authenticated' );
						core.sensor_connections[ data.sensor_array.uuid ] = new SensorConnection( client_connection, data );
						client_connection.definition = core.sensor_connections[ data.sensor_array.uuid ].definition;
					} else {
						console.log( core.getTime(), data.sensor_array.name, 'sensor array not authenticated due to incorrect authentication key' );
					}
				} else if ( json.type === 'user_definition' ) {
					core.broadcast_event( 'onUserDefinition', json );

					console.log( core.getTime(), 'Received user definition from', json.data.user_uuid );

					var data = json.data;
					if ( data.core_auth_key === authentication_key ) {
						console.log( core.getTime(), data.user_uuid, 'user authenticated' );
						core.user_connections[ data.user_uuid ] = new UserConnection( client_connection, data );
						client_connection.definition = core.user_connections[ data.user_uuid ].definition;
					} else {
						console.log( core.getTime(), data.user_uuid, 'user failed authentication' );
					}
				} else if ( json.type === 'sensor_reading' ) {
					if ( client_connection.definition && client_connection.definition.ready ) {
						core.mysql_connection.query( "SELECT COUNT(*) FROM " + core.config.mysql.database + ".sensors WHERE uuid='" + json.sensor_uuid + "'", ( err, result ) => {
							if ( result[ 0 ][ 'COUNT(*)' ] == 0 ) {
								client_connection.close();
							} else {
								core.broadcast_event( 'onSensorReading', json, client_connection );

								console.log( core.getTime(), 'Received reading from', client_connection.definition.sensor_array.name, 'array of', json.data );

								var reading_uuid = core.uuidv4();

								core.mysql_connection.query( "INSERT INTO " + core.config.mysql.database + ".`sensor_" + client_connection.definition.sensors[ json.sensor_uuid ].type.uuid + "` (uuid, sensor_uuid, date, data) VALUES ('" + reading_uuid + "', '" + json.sensor_uuid + "', '" + core.formatDateString( json.date ) + "', '" + json.data + "')", ( err ) => {
									if ( err ) throw err;
								});

								broadcast_sensor_reading( json.sensor_uuid, core.formatDateString( json.date ), json.data );
							}
						});
					}
				} else {
					console.log( core.getTime(), 'Received unknown data', json );
				}
			} catch (e) {
				console.log( 'Error processing JSON:', message.utf8Data, e );
				return;
			}
		});
	});
}

function broadcast_sensor_reading( sensor_uuid, date, data ) {
	for ( var user_uuid in core.user_connections ) {
		var user = core.user_connections[ user_uuid ];

		if ( user.definition.current_view === 'sensor' && user.definition.current_view_uuid === sensor_uuid ) {
			user.connection.sendUTF( JSON.stringify( {
				type: 'sensor_reading',
				date: date,
				data: data
			} ) );
		}
	}
}

core.broadcast_client_data = function( json ) {
	for ( var user_uuid in core.user_connections ) {
		var user = core.user_connections[ user_uuid ];

		user.connection.sendUTF( JSON.stringify( json ) );
	}
}

//
//
//
var UserConnection = function( connection, definition ) {
	this.connection = connection;
	this.definition = definition;
}

var SensorConnection = function( connection, definition ) {
	this.connection = connection;
	this.definition = definition;
}

//
// Return time in human format ([hh:mm:ss])
//
core.getTime = function() {
	var date = new Date();
	var hour = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
	var min = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
	var sec = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();
	return( ( '[' + hour + ':' + min + ':' + sec + ']' ).grey );
}

//
// Format a date string in to one MySQL can use
//
core.formatDateString = function( date ) {
	return new Date( date ).toISOString().slice( 0, 23 ).replace( 'T', ' ' );
}