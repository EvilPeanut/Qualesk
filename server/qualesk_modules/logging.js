var logging = {};

logging.info = function( core, text ) {
	console.log( core.getTime() + " " + text );
}

logging.onSensorReading = function( core, json ) {
	var connection = json[1];
	json = json[0];

	var log_uuid = core.uuidv4();
	var date = core.formatDateString( json.date );

	// 0 is info type
	// 1 is warning type
	// 2 is urgent type
	var severity = 0;

	core.mysql_connection.query("SELECT upper_urgent_boundary, upper_warning_boundary, lower_warning_boundary, lower_urgent_boundary FROM " + core.config.mysql.database + ".sensors WHERE uuid='" + json.sensor_uuid + "'", ( err, result ) => {
		if ( parseFloat( json.data ) > parseFloat( result[ 0 ].upper_urgent_boundary ) || parseFloat( json.data ) < parseFloat( result[ 0 ].lower_urgent_boundary ) ) {
			severity = 2;
		} else if ( parseFloat( json.data ) > parseFloat( result[ 0 ].upper_warning_boundary ) || parseFloat( json.data ) < parseFloat( result[ 0 ].lower_warning_boundary ) ) {
			severity = 1;
		}

		var message = "Received " + connection.definition.sensors[ json.sensor_uuid ].name + " reading of " + json.data;

		core.mysql_connection.query("INSERT INTO " + core.config.mysql.database + ".logs (uuid, sensor_uuid, date, type, message) VALUES ('" + log_uuid + "', '" + json.sensor_uuid + "', '" + date + "', " + severity + ", '" + message + "')", ( err ) => {
			if ( err ) throw err;
		});

		core.broadcast_client_data( {
			type: 'log_entry',
			date: date,
			sensor_uuid: json.sensor_uuid,
			severity: severity,
			message: message
		} );
	});
}

module.exports = logging;