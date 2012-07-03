<?php

class CheckUserDataControl
	extends Control {

	private $is_ok = false;

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('ids', 'string');

		$this->setVar($vars);

		$this->registerTimer('update', 500, true, 'control_'.$this->getVar('id', 'source').'_getUpdateArgs');
	}

	public function update($username='', $password1='', $password2='') {
		if ($username!='' && $password1!='' && $password2!='') { 
			$this->is_ok = (strlen($username) > 2) && ($password1 == $password2);
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
		$rez['is_ok'] = $this->is_ok;
		return $rez;
	}

}