<?php

class DeleteItemControl
	extends Control {

	private $removed = false;

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
		$this->initVar('ids', 'string');

		$this->setVar($vars);		
	}

	private function getObject() {
		$handler = new Handler($this->getVar('object'));
		$obj = $handler->get($this->getVar('item_id'));
		return $obj;
	}

	public function delete() {
		$obj = &$this->getObject();	
		$obj->remove();
		parent::update();
	}

	public function toArray() {
		$rez = parent::toArray();
		$obj = $this->getObject();	
		$rez['exists'] = is_object($obj);
		return $rez;
	}

}
