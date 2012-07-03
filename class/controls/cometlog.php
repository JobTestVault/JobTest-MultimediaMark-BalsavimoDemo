<?php

class CometLogControl
	extends Control {

	private $results = array();

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('ids', 'string');
		$this->initVar('action', 'string');

		$this->setVar($vars);
	}

	public function updateView($data) {
		if (is_array($data['control'])) {
			$class = Control::getClassName($data['control']['control_type']);
			$params = Encryption::simpleDecode($data['control']['control_data']);
			$obj = new $class($params);
			$action = implode('', array_map('ucfirst', explode('_', strtolower($this->getVar('action')))));
			if (is_callable(array($obj, $action))) {
				if ((!isset($data['data'])) || !is_array($data['data']) ) {
					$data['data'] = array();
				}
				$errors = array();
				$msg = array();
				$js = array();
				$obj->$action($data['data'], $msg, $js);
				$this->results['js'] = $js;
				$this->results['msg'] = $msg;
				$this->results['control'] = $obj->toArray();
			} else {
				$this->results['msg'] = array('Nepavyko iškviesti formos išsaugojimo funkcijos :(');
			}
		} else {
			$class = Action::getClassName($this->getVar('action'));
			$obj = new $class();
			$msg = array();
			$js = array();
			$obj->submit($data['data'], $msg, $js);
			$this->results['msg'] = $msg;
			$this->results['js'] = $js;
		}
		parent::update();	
	}

	public function filterAjaxReturnData(&$newData) {
		unset($newData['control_output']);
	}

	public function toArray() {
		$rez = parent::toArray();
		$rez = array_merge($rez, $this->results);
		return $rez;
	}

}
