<?
require 'services/cURL.php';

class HydroVu {

	public $authorization;
	public $cURL;

	function __construct() {
		$this->cURL = new cURL();
	}

	public function authorize( $client_id, $client_secret ) {
		$this->authorization = $this->cURL->get_oauth2_authorization( $client_id, $client_secret );
	}

	public function get_friendly_names() {
		return $this->cURL->http_get( 'https://www.hydrovu.com/public-api/v1/sispec/friendlynames', $this->authorization )['data'];
	}

	public function get_locations() {
		return $this->cURL->http_get( 'https://www.hydrovu.com/public-api/v1/locations/list', $this->authorization )['data'];
	}

	public function get_readings( $location_id,  $location_name ) {
		//Paging is handled through the use of HTTP headers. Responses will have a header X-ISI-Next-Page and a header X-ISI-Prev-Page that can be inserted into a request header X-ISI-Start-Page to fetch the next or previous page respectively. If no start page is provided, the first page will be used. If the response does not have a next or previous page, the headers are not provided.

		//If the data covers multiple pages, the following pages can be retrieved by putting the X-ISI-Next-Page response header value into an X-ISI-Start-Page request header.

		$response = $this->cURL->http_get( 'https://www.hydrovu.com/public-api/v1/locations/' . $location_id . '/data', $this->authorization );

		$response_next_page = $response['header']['X-ISI-Next-Page'];
		$response_first_next_page = $response['header']['X-ISI-Next-Page'];

		// Make paramId list
		$readings = [];

		foreach ($response['data']['parameters'] as $value) {
			$readings[$value['parameterId']] = [];
			$readings[$value['parameterId']]['unitId'] = $value['unitId'];
			$readings[$value['parameterId']]['readings'] = $value['readings'];
		}
		//

		$counter = 0;

		$previous_timestamp = $readings[$value['parameterId']]['readings'][0]['timestamp'];

		while ( true ) {
			echo 'Retrieving page ' . ( $counter + 1 ) . ' of readings beggining ' . gmdate( 'Y-m-d H:i:s', $previous_timestamp ) . ' for location ' . $location_name;

			$page_response = $this->get_readings_page( $location_id, $response_next_page );

			$param_iteration = 0;

			foreach ($page_response['data']['parameters'] as $value) {
				$readings[$value['parameterId']]['readings'] = array_merge($readings[$value['parameterId']]['readings'], $value['readings']);

				if ($param_iteration == 0) {
					$timestamp = $value['readings'][0]['timestamp'];

					if ( $timestamp < $previous_timestamp ) {
						return $readings;
					} else {
						$previous_timestamp = $timestamp;
					}
				}

				$param_iteration++;
			}

			$response_next_page = $page_response['header']['X-ISI-Next-Page'];

			$counter++;
		}

		return $readings;
	}

	public function get_readings_page( $location_id, $page ) {
		return $this->cURL->http_get( 'https://www.hydrovu.com/public-api/v1/locations/' . $location_id . '/data', $this->authorization, array('X-ISI-Start-Page: ' . $page) );
	}

}

?>