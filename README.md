Salesforce REST API for Codeigniter with sparks support
----------------------------------------------------------

Overview
========
A Codeigniter library to interact with the Salesforce.com REST API.  The library currently has 3 methods for interacting with Salesforce.com.

Methods
========
$this->salesforce->get_record();
$this->salesforce->get_query();
$this->salesforce->update_record(); 


Configuration
========
var $instance_url = '<insert your $instance_url here>';
var $api_version = 'v24.0';
var $client_id  = '<insert your $client_id here>';
var $client_secret = '<insert your $client_secret here>';
 
var $username  = '<insert your $username here>';
var $password  = '<insert your $password here>';
var $security_token = '<insert your $security_token here>'; 


Example Usage
========
:: get_record ::
$fields = array('Id','Name','Account.Id');
$results = $this->salesforce->get_record('opportunity','0062000000MjkRo', $fields); 

:: get_query ::
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

:: update_record ::
$data = array('Change_Details__c'=>$this->input->post('request_info'),
              'Renewal_Confirmation__c'=>"Change Requested");
$this->salesforce->update_record('opportunity', '0062000000MjkRo', $data); 

----------------------------------------------------------

you can find the codeigniter thread here:
http://codeigniter.com/forums/viewthread/212536/

Original by Peter Guest from
http://www.peterguest.co.za/?p=5