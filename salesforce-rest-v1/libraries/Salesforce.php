<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* Salesforce API 
 * http://www.salesforce.com/us/developer/docs/api_rest/api_rest.pdf
 * 
 */
class Salesforce {
	
	var $oauth = array();

	/**
	 * Class constructor
	 * @return void
	 */
	public function __construct() {
		$this->load->config('salesforce', TRUE);
		$this->load->library(array('input','encrypt','session'));
		
		
		$this->oauth	= array(
			'client_id'		=>	$this->config->item('sfdc_client_id'),
			'client_secret'	=>  $this->config->item('sfdc_client_secret'),
			'grant_type'	=>  $this->config->item('sfdc_grant_type'),
			'username'		=>	$this->config->item('sfdc_username'),
			'password'		=>	$this->config->item('sfdc_password').$this->config->item('sfdc_security_token')
		);
		
		if($this->session->userdata('access_token') == FALSE):
			$this->auth();
		endif;		
	}
	
	/**
	 * Get CI Instance - replaces old way of $this->ci =& get_instance();
	 * @return bool
	 */
	public function __get($var)
	{
		return get_instance()->$var;
	}

	
	/**
	 * Performs the authentication against Salesforce.com
	 * @return bool
	 */
    public function auth() {
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->config->item('instance_url')."/services/oauth2/token");
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POST, count($this->oauth));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->oauth));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result = json_decode(curl_exec($ch));
		
		if(curl_error($ch)):
   			show_error(curl_getinfo($ch).curl_error($ch));
		else:
			if($this->curl_status_check($ch, $result)):
				$this->set_session($result);
			else:
				return FALSE;
			endif;			
		endif;
	}
	
	/**
	 * Sets the access token of the session variable.
	 * @return bool 
	 */
	public function set_session($result) {
		
		if(isset($result->access_token)):
			$this->session->set_userdata('access_token', $this->encrypt->encode($result->access_token));
		endif;		
	}
	
	/**
	 * Retrieve the access token from the session.
	 * @return string|bool 
	 */
	public function get_token() {
		
		return $this->encrypt->decode($this->session->userdata('access_token'));
	}
	
	/**
	 * Retrieve an sobject record and any specified fields.
	 * @param string $object_name sObject Name
	 * @param string $record_id sObject Record Id
	 * @param mixed $field_list optional Array of fields to return only
	 * @return Object
	 */
	public function get_record($object_name, $record_id, $field_list = FALSE) {
		
		$request = trim($this->config->item('instance_url')).'/services/data/'.$this->api_version.'/sobjects/'.trim($object_name).'/'.trim($record_id);
		
		if($field_list):
			$request .= '?fields='.implode(",",$field_list);
		endif;
		
		return $this->execute_request($request);
	}
	
	public function update_record($object_name, $record_id, $field_list=FALSE) {
		
		$request = trim($this->config->item('instance_url')).'/services/data/'.$this->api_version.'/sobjects/'.trim($object_name).'/'.trim($record_id);
		
		return $this->execute_request($request, "PATCH", json_encode($field_list));
	}
	
	/**
	 * Execute a SOQL query
	 * @return Object
	 */
	public function get_query($query = FALSE) {
		
		$request = trim($this->config->item('instance_url')).'/services/data/'.$this->api_version.'/query/';
		
		if($query):
			$request .= '?q='.urlencode(trim($query));
			return $this->execute_request($request);
		endif;
	}

	public function execute_request($url, $http_method=FALSE, $data=FALSE) {
		
		$ch = curl_init();
		
		if($http_method == "PATCH"):
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $http_method);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 	$data);
		endif;
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					"Authorization: OAuth ".$this->get_token(),
					"Content-type: application/json"));
		
		$result = json_decode(curl_exec($ch));
		
		//Request Error Handling
		if(curl_error($ch)):
			show_error(curl_getinfo($ch).curl_error($ch));
		else:			
			if($this->curl_status_check($ch, $result)):
				return $result;
			else:
				return FALSE;
			endif;		
		endif;
	}
	
	function process_error($result, $code) {
		
		if(is_array($result)):
			show_error($code.": LEVEL1: process_error(): ".$result[0]->errorCode.' : '.$result[0]->message);
		endif;
		
		if(isset($result->error)):
			show_error($code.": LEVEL2: process_error(): ".$result->error.' : '.$result->error_description);
		endif;
	}
	
	function curl_status_check($ch, $result) {
		
		switch(curl_getinfo($ch, CURLINFO_HTTP_CODE)):
				
			case "404":
				show_error("Class: Salesforce - The requested resource could not be found. Check the URI for errors, and verify that there are no sharing issues.");
				break;
				
			case "300":
				show_error("Class: Salesforce - The value used for an external ID exists in more than one record. The response body contains the list of matching records.");
				break;
				
			case "400":
				$this->process_error($result, "400");
				break;
			
			case "415":
				show_error("The entity specified in the request is in a format that is not supported by specified resource for the specified method.");
				break;
				
			case "401":
				$this->process_error($result, "401");
				break;
				
			case "500":
				show_error("Class: Salesforce - An error has occurred within Force.com, so the request could not be completed.");
				break;
					
			case "200":
				return TRUE;
				break;
		
		endswitch;
	}
}
/* End of file Salesforce.php */