<?php

class AnswerObject
	extends Object {

	function __construct($values = array()) {

		parent::__construct(__CLASS__);

		$this->initVar('title', 'string', null, 'atsakymas');
		$this->initVar('question_id', 'int', null, 'susijęs klausimas');
		
		$this->makeLink('vote');

		$this->setDefaultVars($values);
	}

}