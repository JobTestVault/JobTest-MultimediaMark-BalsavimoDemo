<?php

class ProgressBarControl
	extends Control {

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$session = &Session::getInstance();

		$this->initVar('action_id', 'string', md5($session->getID()) );
		$this->initVar('action', 'string');

		$this->setVar($vars);
	}

}