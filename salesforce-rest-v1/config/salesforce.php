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
$config['sfdc_instance_url'] = '';

/*
|--------------------------------------------------------------------------
| API Version
|--------------------------------------------------------------------------
|
| Used to get Salesforce objects references by the API version.
| curl https://na1.salesforce.com/services/data/
|	Winter '11	
|		url: 		/services/data/v20.0
|		version:	20.0
|	Spring '11	
|		url: 		/services/data/v21.0
|		version:	21.0
|	Summer '11	
|		url: 		/services/data/v22.0
|		version:	22.0
|	Winter '12	
|		url: 		/services/data/v23.0
|		version:	23.0
|	Spring '12	
|		url: 		/services/data/v24.0
|		version:	24.0
|	Summer '12	
|		url: 		/services/data/v25.0
|		version:	25.0
|	Winter '12	
|		url: 		/services/data/v26.0
|		version:	26.0
*/
$config['sfdc_api_version'] = 'v26.0';



/*
|--------------------------------------------------------------------------
| OAuth Parameter - Code
|--------------------------------------------------------------------------
|
| Value must be authorization_code for this flow.
|
*/
$config['sfdc_grant_type'] = 'password';

/*
|--------------------------------------------------------------------------
| OAuth Parameter - Code
|--------------------------------------------------------------------------
|
| The Consumer Key from the remote access application definition.
|
*/
$config['sfdc_client_id'] = '';

/*
|--------------------------------------------------------------------------
| OAuth Parameter - Code
|--------------------------------------------------------------------------
|
| The Consumer Secret from the remote access application definition.
|
*/
$config['sfdc_client_secret'] = '';

/*
|--------------------------------------------------------------------------
| OAuth Parameter - Code
|--------------------------------------------------------------------------
|
| The Callback URL from the remote access application definition.
|
*/
$config['sfdc_redirect_uri'] = '';


/*
|--------------------------------------------------------------------------
| OAuth Parameter - Code
|--------------------------------------------------------------------------
|
| Authorization code the consumer must use to obtain the
| access and refresh tokens
|
*/
$config['sfdc_code'] = '';

/*
|--------------------------------------------------------------------------
| OAuth Parameter - Format (optional)
|--------------------------------------------------------------------------
|
| Expected return format. The default is json. Values are:
| 	• urlencoded
| 	• json
| 	• xml
| The return format can also be specified in the header of the
| request using one of the following:
|	Accept: application/x-www-form-urlencoded
|	Accept: application/json
|	Accept: application/xml
|
*/
$config['sfdc_format'] = '';

//SALESFORCE LOGIN PARAMETERS
$config['sfdc_username'] = '';
$config['sfdc_password'] = '';
$config['sfdc_security_token'] = '';


/* End of file config_salesforce.php */
/* Location: ./application/config/config_salesforce.php */
