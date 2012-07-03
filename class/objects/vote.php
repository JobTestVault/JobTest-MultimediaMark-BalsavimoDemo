<?php

class VoteObject
	extends Object {

	function __construct($values = array()) {

		parent::__construct(__CLASS__);

		$this->initVar('ip', 'string', null, 'ip');
		$this->initVar('answer_id', 'int', null, 'atsakymas');
		$this->initVar('question_id', 'int', null, 'klausimas');
		
		$this->setDefaultVars($values);
	}

}