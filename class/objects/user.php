<?php

class UserObject
	extends Object {

	function __construct($values = array()) {

		parent::__construct(__CLASS__);

		$this->initVar('username', 'string', null, 'vartotojo vardas');
		$this->initVar('password', 'string', null, 'slaptažodis');
		
		$this->setDefaultVars($values);
	}

}