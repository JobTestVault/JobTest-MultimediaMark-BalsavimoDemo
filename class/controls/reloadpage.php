<?php

class ReloadPageControl
	extends Control {

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('time', 'int', 5);
		$this->initVar('href', 'string');

		$this->setVar($vars);
	}

}