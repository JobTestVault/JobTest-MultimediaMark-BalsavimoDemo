<?php

class ProgressBarStepControl
	extends Control {

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('step', 'string');
		$this->initVar('ids', 'string');

		$this->setVar($vars);
	}

	/**
	 * Vykdo veiksmą
	 */
	public function doStep($base = '', $params=array()) {
		$base = Encryption::simpleDecode($base);
		$parts = explode(':', $base['action']);
		if (count($parts) > 1) {
			switch (strtolower($parts[0])) {
				case 'control':
					$class = Control::getClassName($parts[1]);
				break;
				case 'action':
				default:
					$class = Action::getClassName($parts[1]);
				break;
			}
		} else {
			$class = Action::getClassName($base['action']);
		}		
		$obj = new $class();
		$action = $this->getVar('step');
		$action = implode('', array_map('ucfirst', explode('_', strtolower($action))));
		if (is_callable(array($obj, $action))) {
			if (!empty($params) && is_array($params)) {
				call_user_func_array(array($obj, $action), $params);
			} else {
				call_user_func(array($obj, $action));
			}
		} 
		$this->updateMode = true;
	}

}