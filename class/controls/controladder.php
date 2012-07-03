<?php

class ControlAdderControl
	extends Control {

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('control', 'string');
		$this->initVar('place', 'string');

		$this->setVar($vars);
	}


	public function add() {
		parent::update();
	}

}