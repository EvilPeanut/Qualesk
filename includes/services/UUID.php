<?

// Modified version of function from https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid

function guidv4()
{
	$data = openssl_random_pseudo_bytes(16);

    $data[ 6 ] = chr( ord( $data[ 6 ] ) & 0x0f | 0x40 );
    $data[ 8 ] = chr( ord( $data[ 8 ] ) & 0x3f | 0x80 );

    return vsprintf( '%s%s-%s-%s-%s-%s%s%s' , str_split( bin2hex( $data ), 4 ) );
}

?>