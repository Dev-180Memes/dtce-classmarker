<?php

Class classmarker_api {

    private $apikey;
    private $secret;
    private $signature;
    private $finished_after_timestamp;
    private $limit;
    private $api_version;
    private $api_base_url;
    private $request_path;
    private $request_parameters;

    public $request_type;

    private $error_message;
    private $response;

    function __construct( $apikey, $secret, $format="json", $api_version=1 ) {

        $this->apiKey = $apikey;
        $this->secret = $secret;
        $this->api_version = $api_version;
        $this->api_base_url = 'https://api.classmarker.com/';
        $this->error_message = false;
        $this->limit = 200;

        $this->setResponseFormat( $format );
    }

    function setResponseFormat( $format="json" ) {
        $format = strtolower($format);

        if ( $format == 'xml' ) {
            $this->format = '.xml';
        } else {
            $this->format = '.json';
        }
    }

    function setApiCredentials(){

		if (!$this->setApiVersion($this->api_version)){
			return false;
		}

		$signature = md5($this->apiKey . $this->secret . time());

		$this->request_parameters = 'api_key='.$this->apiKey.'&signature='.$signature.'&timestamp='.time();

		return true;
	}

    function setApiVersion($api_version){

		/* Only version 1 available at present */
		$available_versions = array(1);

		if ( !in_array($api_version, $available_versions) ){
			$this->error_message = 'Version not available';
			return false;
		}

		$this->api_version = $api_version;

		return true;

	}

    private function setErrorMsg($str){

		$this->error_message = $str;

	}

    function getError(){

		return $this->error_message;

	}

    function doesPreRequestErrorExists(){

		if ($this->error_message !== false){
			return true;
		} else {
			return false;
		}

	}

    function getAvailableGroups(){

		$this->request_type = 'get_available_tests';

		if (!$this->setApiCredentials()){
			return false;
		}

		$this->request_path = '/v'.$this->api_version.$this->format.'?'.$this->request_parameters;

		if ( !$this->makeRequest() ){
			return false;
		}

		return true;

	}

    function getRecentResults($group_type='groups', $finished_after_timestamp=NULL, $limit=200){

		$this->request_type = 'get_results';

		if ($group_type != 'groups' && $group_type != 'links'){

			$this->error_message ='"link" or "group" value not specified';

			return false;
		}

		$this->setApiCredentials();

		if (is_numeric($limit) && $limit <= 200){
			$this->limit = $limit;
		}
		$this->request_parameters .= '&limit='.$this->limit;

		if (is_numeric($finished_after_timestamp) && $finished_after_timestamp >= strtotime("-2 weeks")){
			$this->finished_after_timestamp = $finished_after_timestamp;
		} else {
			$this->finished_after_timestamp = strtotime("-2 weeks");
		}
		$this->request_parameters .= '&finishedAfterTimestamp='.$this->finished_after_timestamp;

		$this->request_path = 'v'.$this->api_version.'/'.$group_type.'/recent_results'.$this->format.'?'.$this->request_parameters;

		if ( !$this->makeRequest() ){
			return false;
		}

		return true;

	}

	function getSingleTestResults($group_type='groups', $group_or_link_id=NULL, $test_id=NULL, $finished_after_timestamp='', $limit=200 ){

		if ($group_type != 'groups' && $group_type != 'links'){
			$this->error_message = 'group_type value "link" or "group" not specified';
			return false;
		}

		if (!is_numeric($group_or_link_id) || !is_numeric($test_id)){
			$this->error_message =  'group_id or test_id missing';
			return false;
		}

		$this->setApiCredentials();

		if (is_numeric($limit) && $limit <= 200){
			$this->limit = $limit;
		}
		$this->request_parameters .= '&limit='.$this->limit;

		if (is_numeric($finished_after_timestamp) && $finished_after_timestamp >= strtotime("-2 weeks")){
			$this->finished_after_timestamp = $finished_after_timestamp;
		}
		$this->request_parameters .= '&finishedAfterTimestamp='.$this->finished_after_timestamp;

		$this->request_path = 'v'.$this->api_version.'/'.$group_type.'/'.$group_or_link_id.'/tests/'.$test_id.'/recent_results'.$this->format.'?'.$this->request_parameters;

		if ( !$this->makeRequest() ){
			return false;
		}

		return true;

	}

	private function makeRequest() {


		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->api_base_url . $this->request_path);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$this->response = curl_exec($ch);

		if ( $this->response === false){
			$this->error_message = 'cURL failed on curl_exec(). Check you have cURL installed on your server.';
			return false;
		}

		curl_close($ch);

	}

	function getResponse() {

		return $this->response;

	}
}

?>