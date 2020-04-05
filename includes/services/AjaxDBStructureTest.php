<?

require_once( 'classes/config.php' );

Config::load();

$host = Config::get( 'database_host' );
$database = Config::get( 'database_name' );
$username = Config::get( 'database_username' );
$password = Config::get( 'database_password' );

mysqli_report( MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX );

$mysqli = new mysqli( $host , $username , $password , $database );

if ( $mysqli->query("SHOW TABLES LIKE 'logins';")->num_rows > 0 ) {
	echo 'Logins table exists<br>';
} else {
	echo 'Logins table does not exist. Creating it...<br>';
	$mysqli->prepare("
		CREATE TABLE `logins` (
		`user_id` char(36) NOT NULL,
		`date` datetime NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	")->execute();
	echo 'Done!<br>';
}

if ( $mysqli->query("SHOW TABLES LIKE 'users';")->num_rows > 0 ) {
	echo 'Users table exists<br>';
} else {
	echo 'Users table does not exist. Creating it...<br>';
	$mysqli->prepare("
		CREATE TABLE `users` (
		`user_id` char(36) NOT NULL,
		`username` varchar(240) NOT NULL,
		`email` varchar(240) NOT NULL,
		`password` char(128) NOT NULL,
		`password_salt` char(128) NOT NULL,
		PRIMARY KEY (`user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	")->execute();
	echo 'Done!<br>';
}

if ( $mysqli->query("SHOW TABLES LIKE 'user_permissions';")->num_rows > 0 ) {
	echo 'User permissions table exists<br>';
} else {
	echo 'User permissions table does not exist. Creating it...<br>';
	$mysqli->prepare("
		CREATE TABLE `user_permissions` (
		`user_id` char(36) NOT NULL,
		`admin_features` BOOLEAN NOT NULL DEFAULT FALSE,
		PRIMARY KEY (`user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	")->execute();
	echo 'Done!<br>';
}

if ( $mysqli->query("SHOW TABLES LIKE 'systems';")->num_rows > 0 ) {
	echo 'Systems table exists<br>';
} else {
	echo 'Systems table does not exist. Creating it...<br>';
	$mysqli->prepare("
		CREATE TABLE `systems` (
		`uuid` char(36) NOT NULL,
		`name` tinytext NOT NULL,
		`description` tinytext,
		PRIMARY KEY (`uuid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	")->execute();
	echo 'Done!<br>';
}

if ( $mysqli->query("SHOW TABLES LIKE 'sensor_arrays';")->num_rows > 0 ) {
	echo 'Sensor arrays table exists<br>';
} else {
	echo 'Sensor arrays table does not exist. Creating it...<br>';
	$mysqli->prepare("
		CREATE TABLE `sensor_arrays` (
		`uuid` char(36) NOT NULL,
		`system_uuid` char(36) NOT NULL,
		`name` tinytext NOT NULL,
		`longitude` double,
		`latitude` double,
		`description` tinytext,
		PRIMARY KEY (`uuid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	")->execute();
	echo 'Done!<br>';
}

if ( $mysqli->query("SHOW TABLES LIKE 'sensors';")->num_rows > 0 ) {
	echo 'Sensors table exists<br>';
} else {
	echo 'Sensors table does not exist. Creating it...<br>';
	$mysqli->prepare("
		CREATE TABLE `sensors` (
		`uuid` char(36) NOT NULL,
		`sensor_array_uuid` char(36),
		`sensor_type` tinytext NOT NULL,
		`name` tinytext NOT NULL,
		`description` tinytext,
		`upper_urgent_boundary` tinytext,
		`upper_warning_boundary` tinytext,
		`lower_warning_boundary` tinytext,
		`lower_urgent_boundary` tinytext,
		`configuration` JSON,
		PRIMARY KEY (`uuid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	")->execute();
	echo 'Done!<br>';
}

if ( $mysqli->query("SHOW TABLES LIKE 'sensor_types';")->num_rows > 0 ) {
	echo 'Sensor types table exists<br>';
} else {
	echo 'Sensor types table does not exist. Creating it...<br>';
	$mysqli->prepare("
		CREATE TABLE `sensor_types` (
		`uuid` char(36) NOT NULL,
		`name` tinytext NOT NULL,
		`description` tinytext,
		`unit` tinytext NOT NULL,
		PRIMARY KEY (`uuid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	")->execute();
	echo 'Done!<br>';
}

if ( $mysqli->query("SHOW TABLES LIKE 'graphs';")->num_rows > 0 ) {
	echo 'Graphs table exists<br>';
} else {
	echo 'Graphs table does not exist. Creating it...<br>';
	$mysqli->prepare("
		CREATE TABLE `graphs` (
		`uuid` char(36) NOT NULL,
		`sensor_array_uuid` char(36) NOT NULL,
		`name` tinytext NOT NULL,
		`description` tinytext,
		`sensors` text,
		`configuration` JSON,
		PRIMARY KEY (`uuid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	")->execute();
	echo 'Done!<br>';
}

if ( $mysqli->query("SHOW TABLES LIKE 'logs';")->num_rows > 0 ) {
	echo 'Logs table exists<br>';
} else {
	echo 'Logs table does not exist. Creating it...<br>';
	$mysqli->prepare("
		CREATE TABLE `logs` (
		`uuid` char(36) NOT NULL,
		`sensor_uuid` char(36) NOT NULL,
		`date` datetime(3) NOT NULL,
		`type` int DEFAULT 0,
		`message` text NOT NULL,
		PRIMARY KEY (`uuid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	")->execute();
	echo 'Done!<br>';
}

if ( $mysqli->query("SHOW TABLES LIKE 'dashboards';")->num_rows > 0 ) {
	echo 'Dashboards table exists<br>';
} else {
	echo 'Dashboards table does not exist. Creating it...<br>';
	$mysqli->prepare("
		CREATE TABLE `dashboards` (
		`uuid` char(36) NOT NULL,
		`name` tinytext NOT NULL,
		`description` tinytext,
		`dash_json` JSON,
		PRIMARY KEY (`uuid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	")->execute();
	echo 'Done!<br>';
}

?>