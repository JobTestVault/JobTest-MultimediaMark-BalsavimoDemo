<?php

class FormTextControl
	extends Control {

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('fname', 'string');
		$this->initVar('value', 'string');
		$this->initVar('readonly', 'bool');

		$this->setVar($vars);
	}

}