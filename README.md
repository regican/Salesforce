##Salesforce REST API for Codeigniter with sparks support

###Overview
A Codeigniter library to interact with the Salesforce.com REST API.


###Methods
to load the module:

	$this->load->spark('salesforce-rest-v1');

available methods:

	$this->salesforce->get(OBJECT, ID, DATA);
	$this->salesforce->create(OBJECT, DATA);
	$this->salesforce->update(OBJECT, ID, DATA);
	$this->salesforce->upsert(OBJECT, KEY, VALUE, DATA);
	$this->salesforce->query(QUERY);
	$this->salesforce->search(QUERY);
	
	$this->salesforce->auth();
	$this->salesforce->get_token();
	$this->salesforce->save_token(TOKEN);

###Configuration
the configuration file is located at config/salesforce.php

	'username' 			=> '',
	'password'			=> '',
	'security_token'	=> '',
	
	'consumer_key'		=> '',
	'consumer_secret'	=> '',
	'redirect_url'		=> '',


###Example Usage
***get()***
Example for retrieving values from fields on an Account object

	$results = $this->salesforce->get('Account','001D000000INjVe', array('AccountNumber', 'BillingPostalCode')); 

Example usage for retrieving a Merchandise__c record using an external ID

	$results = $this->salesforce->get('Merchandise__c','MerchandiseExtID__c/123'); 


***create()***
Example for creating a new Account

	$results = $this->salesforce->create('Account', array('Name' => 'Regican'))


***update()***
Example usage for updating fields in a Document object

	$this->salesforce->update('Document', '015D0000000N3ZZIA0', 
		array(
			'Name' => 'Updated Name', 
		)
	); 


***upsert()***
Example for upserting a record that does not yet exist

	$this->salesforce->upsert('Account', 'customExtIdField__c', '11999',
		array(
			'Name' => 'Upserted Name', 
		)
	);
	

***query()***
Example usage for executing a query

	$results = $this->salesforce->query("SELECT name FROM Account");

Example usage for executing a query for deleted Merchandise__c records

	$results = $this->salesforce->query("SELECT Name FROM Merchandise__c WHERE isDeleted = TRUE");


***search()***
Example usage

	$results = $this->salesforce->query("FIND {test}");

