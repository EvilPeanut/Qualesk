var detection = {};

detection.onSensorArrayDefinition = function( core, json ) {
	var definition = json[0];
	var sensor_array = definition.sensor_array;

	//
	// Check if sensor array exists and create if not
	//
	core.mysql_connection.query("SELECT uuid FROM " + core.config.mysql.database + ".sensor_arrays WHERE uuid='" + sensor_array.uuid + "' LIMIT 1", ( err, result ) => {
		if ( result.length == 0 ) {
			//
			// Create sensor array
			//
			core.mysql_connection.query("INSERT INTO " + core.config.mysql.database + ".sensor_arrays (uuid, system_uuid, name, description, latitude, longitude) VALUES ('" + sensor_array.uuid + "', '" + sensor_array.system_uuid + "', '" + sensor_array.name + "', '" + sensor_array.description + "', " + sensor_array.latitude + ", " + sensor_array.longitude + ")", ( err ) => {
				if ( err ) throw err;
			});
		}
	});

	//
	// Check if sensor already exists
	//
	var loaded = 0;
	var sensor_number = 0;

	for ( var sensor_uuid in definition.sensors ) {
		setTimeout( function( sensor_uuid ) {
			detection.create_sensor( core, definition, sensor_uuid, sensor_array, () => {
				loaded++;

				if ( Object.keys( definition.sensors ).length == loaded ) {
					core.sensor_connections[ sensor_array.uuid ].definition.ready = true;
				}
			} );
		}, sensor_number * 10, sensor_uuid );

		sensor_number++;
	}
}

detection.create_sensor = function( core, definition, sensor_uuid, sensor_array, onComplete ) {
	var sensor = definition.sensors[ sensor_uuid ];
	var sensor_type = sensor.type;

	//
	// Create sensor type if it doesn't exist
	//
	core.mysql_connection.query("SELECT uuid FROM " + core.config.mysql.database + ".sensor_types WHERE uuid='" + sensor_type.uuid + "' LIMIT 1", ( err, result ) => {
		if ( result.length == 0 ) {
			core.mysql_connection.query("INSERT INTO " + core.config.mysql.database + ".sensor_types (uuid, name, description, unit) VALUES ('" + sensor_type.uuid + "', '" + sensor_type.name + "', '" + sensor_type.description + "', '" + sensor_type.unit + "')", ( err ) => {
				if ( err ) throw err;
			});

			core.mysql_connection.query("CREATE TABLE " + core.config.mysql.database + ".`sensor_" + sensor_type.uuid + "` (`uuid` char(36) NOT NULL, `sensor_uuid` char(36) NOT NULL, `date` datetime(3) NOT NULL, `data` " + sensor_type.data_type + " NOT NULL, `anomaly` boolean NOT NULL DEFAULT 0, PRIMARY KEY (`uuid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8", ( err ) => {
				if ( err ) throw err;
			});
		}
	});

	//
	// Create sensor if it doesn't exist
	//
	core.mysql_connection.query("SELECT uuid FROM " + core.config.mysql.database + ".sensors WHERE uuid='" + sensor_uuid + "' LIMIT 1", ( err, result ) => {
		if ( result.length == 0 ) {
			core.mysql_connection.query("INSERT INTO " + core.config.mysql.database + ".sensors (uuid, sensor_array_uuid, sensor_type, name, description) VALUES ('" + sensor_uuid + "', '" + sensor_array.uuid + "', '" + sensor_type.uuid + "', '" + sensor.name + "', '" + sensor.description + "')", ( err ) => {
				if ( err ) throw err;
			});

			onComplete();

			//TODO: Log sensor record creation
		} else {
			onComplete();
		}
	});
}

module.exports = detection;