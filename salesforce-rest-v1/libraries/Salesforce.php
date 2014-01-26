<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* Salesforce API 
 * http://www.salesforce.com/us/developer/docs/api_rest/api_rest.pdf
 * 
 */
class Salesforce {
	
	var $cnf = array();

	/**
	 * Class constructor
	 * @return void
	 */
	public function __construct() {
		$this->load->config('salesforce');
		$this->cnf = $this->config->item('salesforce-rest');	
		
		if (!$this->cnf['use_session'] && session_id() == '') {
			session_start();	
		}
		
		if ( !$this->get_token() ){
			$auth = $this->auth();
			$this->save_token($auth['result']->access_token);
		}
	}
	
	/**
	 * Get CI Instance - replaces old way of $this->ci =& get_instance();
	 * @return bool
	 */
	public function __get($var) {
		return get_instance()->$var;
	}

	
	/**
	 * Performs the authentication against Salesforce.com
	 */
    public function auth() {
		$response = array(
			'success' => FALSE,
			'elapsed' => microtime(TRUE)	//for benchmarking
		);
		
		//prepare data for auth and auth process
		$data = array(
			'client_id'		=>	$this->cnf['consumer_key'],
			'client_secret'	=>  $this->cnf['consumer_secret'],
			'redirect_uri'	=> 	$this->cnf['redirect_url'],
			
			'username'		=>	$this->cnf['username'],
			'password'		=>	$this->cnf['password'].$this->cnf['security_token']
		);
		
		//initiate curl call with endpoint and prepared data
		$ch   = curl_init();
				curl_setopt_array($ch, array(
						CURLOPT_URL				=> $this->cnf['endpoints']['oauth2'],
						CURLOPT_HEADER			=> FALSE,
						CURLOPT_SSL_VERIFYPEER	=> FALSE,
						CURLOPT_POST			=> count($data),
						CURLOPT_POSTFIELDS		=> http_build_query($data).'&grant_type=password',
						CURLOPT_RETURNTRANSFER	=> 1
				));
		
		//save response
		$response['result'] = curl_exec($ch);
		$response['error'] 	= curl_error($ch);
		$response['status'] = $this->_curl_status(curl_getinfo($ch, CURLINFO_HTTP_CODE), $response['result']);
		$response['elapsed'] = microtime(TRUE) - $response['elapsed'];
		
		//update return array for successful calls
		if ($response['result']){
			$response['success'] = TRUE;
			$response['result']  = json_decode($response['result']);
		}
		
		if ($this->cnf['debug']){
			echo '<pre>'.__METHOD__. " --> ";
			print_r($response);
			echo '</pre>';
		}
		return $response;
	}
	
	/**
	 * Save the access token for future use.
	 */
	public function save_token($token = '') {
		$response = array('success' => FALSE);
		
		try {
			if (empty($token)){
				throw new Exception('token empty');		
			}else {
				if ($this->cnf['encrypt']){
					$token = $this->encrypt->encode($token);
				}
			}
			
			if ($this->cnf['use_session']){
				$this->session->set_userdata('access_token', $token);
			}else {
				$_SESSION['access_token'] = $token;
			}
			
			$response['success'] = TRUE;
			
		}catch(Exception $e){
			$response['errors'][] = $e->getMessage();
		}
		
		return $response;
	}
	
	/**
	 * Retrieve the access token from the session.
	 * @return string|bool 
	 */
	public function get_token() {
		$token = FALSE;
		
		if ($this->cnf['use_session']){
			$token = ($this->session->userdata('access_token')) ? $this->session->userdata('access_token') : FALSE;
		}else {
			$token = isset($_SESSION['access_token']) ? $_SESSION['access_token'] : FALSE;
		}
		
		if (!empty($token) && $this->cnf['encrypt']){
			$token = $this->encrypt->decode($token);
		}
		
		return $token;
	}
	
	/**
	 * Get an Salesforce Object
	 * @return array 
	 */
	public function get($object_name, $object_id, $field_list = array()){
		$response = array(
			'success' 	=> FALSE,
			'elapsed' 	=> microtime(TRUE)	//for benchmarking
		);
		
		try {
			$url = $this->cnf['endpoints']['sobjects']."/{$object_name}/{$object_id}";
			if (!empty($field_list)){
				$url .= "?fields=".implode(",",$field_list);	
			}
			
			$call = $this->_execute('GET', $url, $field_list);
			if ($call){
				foreach($call as $key => $val){
					$response[$key] = $val;	
				}
			}else {
				throw new Exception(__METHOD__.' execute call failed');	
			}
			
		}catch(Exception $e){
			$response['error'] = $e->getMessage();
		}
		
		$response['elapsed'] = microtime(TRUE) - $response['elapsed'];
		
		return $response;
	}
	
	/**
	 * Create an Salesforce Object
	 * @return array 
	 */
	public function create($object_name, $field_list = array()){
		$response = array(
			'success' 	=> FALSE,
			'elapsed' 	=> microtime(TRUE)	//for benchmarking
		);
		
		try {
			$url = $this->cnf['endpoints']['sobjects']."/{$object_name}";
			$call = $this->_execute('POST', $url, $field_list);
			if ($call){
				foreach($call as $key => $val){
					$response[$key] = $val;	
				}
			}else {
				throw new Exception(__METHOD__.' execute call failed');	
			}
			
		}catch(Exception $e){
			$response['error'] = $e->getMessage();
		}
		
		$response['elapsed'] = microtime(TRUE) - $response['elapsed'];
		
		return $response;
	}
	
	/**
	 * Update an Salesforce Object using an ID
	 * @return array 
	 */
	public function update($object_name, $object_id, $field_list = array()){
		$response = array(
			'success' 	=> FALSE,
			'elapsed' 	=> microtime(TRUE)	//for benchmarking
		);
		
		try {
			$url = $this->cnf['endpoints']['sobjects']."/{$object_name}/{$object_id}";
			$call = $this->_execute('PATCH', $url, $field_list);
			if ($call){
				foreach($call as $key => $val){
					$response[$key] = $val;	
				}
			}else {
				throw new Exception(__METHOD__.' execute call failed');	
			}
			
		}catch(Exception $e){
			$response['error'] = $e->getMessage();
		}
		
		$response['elapsed'] = microtime(TRUE) - $response['elapsed'];
		
		return $response;
	}
	
	/**
	 * Upsert using a Key and ID an Salesforce Custom Object
	 * @return array 
	 */
	public function upsert($object_name, $upsert_key, $upsert_val, $field_list = array()){
		$response = array(
			'success' 	=> FALSE,
			'elapsed' 	=> microtime(TRUE)	//for benchmarking
		);
		
		try {
			$url = $this->cnf['endpoints']['sobjects']."/{object_name}/{$upsert_key}/{$upsert_key}";
			$call = $this->_execute('PATCH', $url, $field_list);
			if ($call){
				foreach($call as $key => $val){
					$response[$key] = $val;	
				}
			}else {
				throw new Exception(__METHOD__.' execute call failed');	
			}
			
		}catch(Exception $e){
			$response['error'] = $e->getMessage();
		}
		
		$response['elapsed'] = microtime(TRUE) - $response['elapsed'];
		
		return $response;
	}
	
	/**
	 * Execute a SOQL query
	 * @return array 
	 */
	public function query($query = ''){
		$response = array(
			'success' 	=> FALSE,
			'query' 	=> $query,
			'elapsed' 	=> microtime(TRUE)	//for benchmarking
		);
		
		try {
			if (empty($query)){
				throw new Exception(__METHOD__.' query not supplied');	
			}
			
			$url = $this->cnf['endpoints']['query']."?q=".urlencode($query);
			$call = $this->_execute('GET', $url);
			if ($call){
				foreach($call as $key => $val){
					$response['result'] = $call;
				}
			}else {
				throw new Exception(__METHOD__.' execute call failed');	
			}
			
			$response['success'] = TRUE;
		}catch(Exception $e){
			$response['error'] = $e->getMessage();
		}
		
		$response['elapsed'] = microtime(TRUE) - $response['elapsed'];
		
		return $response;
	}
	
	/**
	 * Search Salesforce using an SOSL query
	 * @return array 
	 */
	public function search($query = ''){
		$response = array(
			'success' 	=> FALSE,
			'search' 	=> $query,
			'elapsed' 	=> microtime(TRUE)	//for benchmarking
		);
		
		try {
			if (empty($query)){
				throw new Exception(__METHOD__.' search query not supplied');	
			}
			
			$url = $this->cnf['endpoints']['search']."?q=".urlencode($query);
			$call = $this->_execute('GET', $url);
			if ($call){
				$response['result'] = $call;
			}else {
				throw new Exception(__METHOD__.' execute call failed');	
			}
			
			$response['success'] = TRUE;
		}catch(Exception $e){
			$response['error'] = $e->getMessage();
		}
		
		$response['elapsed'] = microtime(TRUE) - $response['elapsed'];
		
		return $response;
	}
	
	/**
	 * (private) executes curl call to salesforce
	 * @return array 
	 */
	private function _execute($method = 'GET', $url = '', $data = array()){
		$response = array('success' => FALSE);
		$curl_options = array(
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer ".$this->get_token(),
				"Content-type: application/json"
			),
			CURLOPT_URL => $url,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_RETURNTRANSFER => TRUE
		);
		
		switch($method){
			default:
			case 'GET':
				$url = ($data) ? $url."?".http_build_query($data) : $url;
				$curl_options[CURLOPT_URL] = $url;
			break;
			
			case 'PATCH':
			case 'POST':
				$data = json_encode($data);
				$curl_options[CURLOPT_POST] = TRUE;
				$curl_options[CURLOPT_POSTFIELDS] = $data;
				$curl_options[CURLOPT_HTTPHEADER][] = "Content-length: ".strlen($data);
			break;
		}
		
		$ch = curl_init();
			  curl_setopt_array($ch, $curl_options);
		
		//save response
		$response['result'] = curl_exec($ch);
		$response['error'] 	= curl_error($ch);
		$response['code'] 	= curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response['status'] = $this->_curl_status($response['code'], $response['result']);
		$response['elapsed'] = microtime(TRUE) - $response['elapsed'];
		
		//update return array for successful calls
		if ($response['result']){
			$response['success'] = TRUE;
			$response['result']  = json_decode($response['result']);
		}
		
		//PATCH method doesn't have any return
		elseif (!$response['result'] && $response['code'] == 204){
			$url  = parse_url($url); 				// parse the url
     		$path = explode("/", $keys['path']); 	// splitting the path
	 
			$response['result'] = (object) array(
				'id' => end($path),					// get the value of the last element containing the id
				'success' => TRUE,
				'errors' => array()
			);
		}
		
		if ($this->cnf['debug']){
			echo '<pre>'.__METHOD__. " --> ";
			print_r($response);
			echo '</pre>';
		}
		
		return $response;
	}
	
	/**
	 * (private) get the status of the curl call
	 * @return string 
	 */
	private function _curl_status($http_code, $result) {
		$status = "";
		
		switch($http_code) {
			case "500":
				$status = "Class: Salesforce - An error has occurred within Force.com, so the request could not be completed.";
				break;
				
			case "415":
				$status = "The entity specified in the request is in a format that is not supported by specified resource for the specified method.";
				break;
			
			case "404":
				$status = "Class: Salesforce - The requested resource could not be found. Check the URI for errors, and verify that there are no sharing issues.";
				break;	
			
			case "400":
			case "401":
				$status = $this->_process_error($http_code, $result);
				break;
			
			case "300":
				$status = "Class: Salesforce - The value used for an external ID exists in more than one record. The response body contains the list of matching records.";
				break;
				
			case "204":
			case "200":
				$status = "{$http_code} OK";
			default:
		}
		
		return $status;
	}
	
	/**
	 * (private) get the full error message
	 * @return string 
	 */
	private function _process_error($http_code, $result) {
		if (is_array($result)){
			return $http_code.": LEVEL1: process_error(): ".$result[0]->errorCode.' : '.$result[0]->message;
		}
		
		if (isset($result->error)){
			return $http_code.": LEVEL2: process_error(): ".$result->error.' : '.$result->error_description;
		}
	}
}

/* End of file Salesforce.php */