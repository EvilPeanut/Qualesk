<?

class cURL {

	public static function get_oauth2_authorization( $client_id, $client_secret ) {
		$curl = curl_init();

		// DEBUG ONLY
		// See https://stackoverflow.com/questions/21187946/curl-error-60-ssl-certificate-issue-self-signed-certificate-in-certificate-cha
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://www.hydrovu.com/public-api/oauth/token?grant_type=client_credentials&client_id=" . $client_id . "&client_secret=" . $client_secret,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "",
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			// TODO: Handle error
			echo "cURL Error #:" . $err;
		} else {
			$response = json_decode($response, true);
			return $response['token_type'] . ' ' . $response['access_token'];
		}
	}

	public static function http_get( $url, $authorization, $header = array() ) {
		$curl = curl_init();

		// DEBUG ONLY
		// See https://stackoverflow.com/questions/21187946/curl-error-60-ssl-certificate-issue-self-signed-certificate-in-certificate-cha
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_POSTFIELDS => "",
			CURLOPT_HTTPHEADER => array_merge(array(
				"authorization: " . $authorization
			), $header)
		));

		// Handle headers
		$headers = [];

		curl_setopt($curl, CURLOPT_HEADERFUNCTION,
			function($curl, $header) use (&$headers)
			{
				$header_len = strlen($header);

				if (strpos($header, ':') !== false) {
					$header_explode = explode(':', $header, 2);

					$header_name = $header_explode[0];
					$header_value = trim($header_explode[1]);

					$headers[$header_name] = $header_value;
				}

				return $header_len;
			}
		);
		//

		$response = [];

		$response['data'] = json_decode(curl_exec($curl), true);
		$response['header'] = $headers;
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			// TODO: Handle error
			echo "cURL Error #:" . $err;
		} else {
			return $response;
		}
	}

}

?>