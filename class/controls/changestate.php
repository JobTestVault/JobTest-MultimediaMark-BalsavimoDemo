<?php

class ChangeStateControl
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
		$this->initVar('onlyone', 'bool');

		$this->registerTimer('update', 1000);

		$this->setVar($vars);
	}

	private function getHandler() {
		static $handler = null;
		if (!is_object($handler)) {
			$handler = new Handler($this->getVar('object'));
		}
		return $handler;
	}

	private function getObject() {
		static $obj = null;
		if (!is_object($obj)) {
			$handler = &$this->getHandler();
			$obj = $handler->get($this->getVar('item_id'));
		}
		return $obj;
	}

	public function change() {
		$obj = &$this->getObject();		
		$value = ((bool)$obj->getVar($this->getVar('var'), 'source') == 1)?true:false;
		$obj->setVar($this->getVar('var'),  !$value);
		if ((bool)$this->getVar('onlyone', 'source')) {
			$handler = &$this->getHandler();
			$handler->update(array('active' => 0));
		}
		$obj->save();
		parent::update();
	}

	public function toArray() {
		$rez = parent::toArray();
		$obj = &$this->getObject();	
		$rez['value'] = $obj->getVar($this->getVar('var'), 'source');
		return $rez;
	}

}
