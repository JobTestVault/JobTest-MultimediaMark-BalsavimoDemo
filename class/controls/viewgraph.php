<?php

class ViewGraphControl
	extends Control {

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('var', 'string');
		$this->initVar('object', 'string');
		$this->initVar('item_id', 'int');

		$this->setVar($vars);
	}

	private function getObject() {
		$handler = new Handler($this->getVar('object'));
		$obj = $handler->get($this->getVar('item_id'));
		return $obj;
	}

	public function toArray() {
		$rez = parent::toArray();
		$obj = $this->getObject();	
		$rez['id_name'] = $obj->getIDName();
		return $rez;
	}

}
