<?php

class LoginControl
	extends Control {

	private $wasError = false;
	private $loged = false;
	private $username = '';
	private $session_id ='';

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);
		$this->setVar($vars);
	}

	/**
	 * Bandoma prisijungti
	 *
	 * @param string $username
	 * @param string $password
	 */
	 public function login($username, $password) {
		 $session = &Session::getInstance();
		 $this->wasError = !$session->login($username, $password);
		 if (!$this->wasError) {
			$this->loged = true;
			$this->username = $username;
			$this->session_id = $session->getID();
		 }
		 parent::update();
	 }

	/**
	 * Verčiame visa tai į masyvą
	 *
	 * @return string
	 */
	public function toArray() {
		$rez = parent::toArray();
		$rez['wasError'] = $this->wasError;
		$rez['loged'] = $this->loged;
		$rez['username'] = $this->username;
		$rez['session_id'] = $this->session_id;
		return $rez;
	}

}