<?php

class WizardStepControl
	extends Control {

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('validator_id', 'string');

		$this->initVar('disable_back', 'bool', false);
		$this->initVar('automove', 'bool', false);
		$this->initVar('hidebuttons', 'bool', false);		

		$this->styles['display'] = 'none';
		$this->styles['z-index'] = 63876;

		$this->setVar($vars);
	}

}