<?php

class QuestionObject
	extends Object {

	function __construct($values = array()) {

		parent::__construct(__CLASS__);

		$this->initVar('title', 'string', null, 'klausimas');
		$this->initVar('active', 'bool', false, 'ar aktyvus?');
		
		$this->makeLink('answer');

		$this->setDefaultVars($values);
	}


}