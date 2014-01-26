<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Example extends CI_Controller {

	public function index(){
		$this->load->spark('salesforce-rest-v1');
		
		$data = array(
			'Name' => 'Regican.'.time()
		);
		
		$create = $this->salesforce->create('Account', $data);
		$update = $this->salesforce->update('Account', $create['result']->id, $data);
		$upsert = $this->salesforce->upsert('Account','Email_Key__c', 'regican+upsert.test@gmail.com', $data);
		
	}
}

/* End of file example.php */
/* Location: ./application/controllers/Example.php */