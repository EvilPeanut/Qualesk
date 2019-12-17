<?

$host = $_REQUEST[ "host" ];
$database = $_REQUEST[ "database" ];
$username = $_REQUEST[ "username" ];
$password = $_REQUEST[ "password" ];

mysqli_report( MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX );

try {
	new mysqli( $host , $username , $password , $database );
	echo 1;
} catch ( mysqli_sql_exception $exception ) { 
	echo 0;
}

?>