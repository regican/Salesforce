##Salesforce REST API for Codeigniter with sparks support
- - -

###Overview
A Codeigniter library to interact with the Salesforce.com REST API.  The library currently has 3 methods for interacting with Salesforce.com.

###Methods
to load the module
	$this->load->sparks('salesforce-rest-v1');

to access methods:
	$this->salesforce->get_record();
	$this->salesforce->get_query();
	$this->salesforce->update_record(); 

###Configuration
the configuration file is located at config/salesforce.php
	$config['sfdc_instance_url'] = '';
	$config['sfdc_api_version'] = 'v26.0';
	$config['sfdc_client_id'] = '';
	$config['sfdc_client_secret'] = '';
	 
	$config['sfdc_username'] = '';
	$config['sfdc_password'] = '';
	$config['sfdc_security_token'] = '';


###Example Usage
get_record:
	$fields = array('Id','Name','Account.Id');
	$results = $this->salesforce->get_record('opportunity','0062000000MjkRo', $fields); 

get_query:
	$results = $this->salesforce->get_query(
	     "SELECT Id, 
	     Account.Name, 
	     CloseDate, 
	     CurrencyIsoCode, 
	     Account.BillingPostalCode,
	     Account.BillingStreet,
	     Account.BillingState,
	     Account.BillingCity,
	     Account.BillingCountry,
	     Renewal_Confirmation__c,
	     StageName,
	     Amount, 
	     (SELECT Id, Description, PricebookEntry.Name, Licenced_Product__r.Id, Quantity, UnitPrice, ListPrice, TotalPrice FROM OpportunityLineItems WHERE Do_Not_Renew__c != true ORDER BY Description ASC) 
	     FROM Opportunity WHERE Id='0062000000MjkRo'"); 

update_record:
	$data = array('Change_Details__c'=>$this->input->post('request_info'), 'Renewal_Confirmation__c'=>"Change Requested"); 
	$this->salesforce->update_record('opportunity', '0062000000MjkRo', $data); 

- - -

You can find the codeigniter thread here: http://codeigniter.com/forums/viewthread/212536/
Original by Peter Guest from http://www.peterguest.co.za/?p=5