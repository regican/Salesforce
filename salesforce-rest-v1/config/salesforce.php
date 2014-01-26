<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Instance URL
|--------------------------------------------------------------------------
|
| Identifies the Salesforce instance to which API calls should
| be sent.
|
*/
$url = 'https://na9.salesforce.com/';


/*
|--------------------------------------------------------------------------
| API Version
|--------------------------------------------------------------------------
|
| Used to get Salesforce objects references by the API version.
*/
$v = "v26.0";


/*
|--------------------------------------------------------------------------
| Auth and Auth Config Array
|--------------------------------------------------------------------------
*/
$config['salesforce-rest'] = array(
	/*
	| Debug Output
	|  - Prints out the return of each call in standard output
	*/
	'debug'				=> TRUE,
	
	/*
	| Encrypt Access Token
	|  - Use's Codeigniter Encrypt class to encrypt the token before saving
	|	 the token to the session
	*/
	'encrypt'			=> FALSE,
	
	/*
	| Use Codeigniter's Session 
	|  - TRUE	: Will make use of Codeigniter's session class to save the token
	|  - FALSE	: Will maek use PHP's Native session
	*/
	'use_session'		=> TRUE,
	
	/*
	| Salesforce Auth n Auth Requirements
	|  - uses the Web Server Flow for the OAuth 2.0 authentication flow
	*/
	'username' 			=> '',
	'password'			=> '',
	'security_token'	=> '',
	
	'consumer_key'		=> '',
	'consumer_secret'	=> '',
	'redirect_url'		=> '',
	
	/* --- end of user config --- */
	'instance_url'		=> $url,
	'endpoints'			=> array(
								'oauth2'	=> "{$url}services/oauth2/token",								
								'sobjects' 	=> "{$url}services/data/{$v}/sobjects", 
								'query' 	=> "{$url}services/data/{$v}/query", 
								'search' 	=> "{$url}services/data/{$v}/search", 
								'licensing' => "{$url}services/data/{$v}/licensing", 
								'connect' 	=> "{$url}services/data/{$v}/connect", 
								'tooling' 	=> "{$url}services/data/{$v}/tooling", 
								'chatter' 	=> "{$url}services/data/{$v}/chatter", 
								'recent' 	=> "{$url}services/data/{$v}/recent" 
						  )
);
