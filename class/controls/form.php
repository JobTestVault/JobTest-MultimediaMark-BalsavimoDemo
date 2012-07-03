<?php

class FormControl
	extends Control {

	private $results = array();

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('action', 'string');
		$this->initVar('method', 'string', 'post');
		$this->initVar('ids', 'string');

		$this->setVar($vars);
	}

	/**
	 * Šios funkcijos pagalba apdorojame Ajax'inius duomenis
	 *
	 * @param array $data
	 */
	public function submit($data) {
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
				$js = array();
				$obj->$action($data['data'], $errors, $js);
				$this->results['errors'] = $errors;
				$this->results['js'] = $js;
				$this->results['control'] = $obj->toArray();
			} else {
				$this->results['errors'] = array('Nepavyko iškviesti formos išsaugojimo funkcijos :(');
			}
		} else {
			$class = Action::getClassName($this->getVar('action'));
			$obj = new $class();
			$errors = array();
			$js = array();
			$obj->submit($data['data'], $errors, $js);
			$this->results['errors'] = $errors;
			$this->results['js'] = $js;
		}
		parent::update();		
	}

	public function toArray() {
		$rez = parent::toArray();
		$rez['after_submit_action_name'] = 'on' . implode('', array_map('ucfirst', explode('_', strtolower($this->getVar('action')))));
		$rez = array_merge($rez, $this->results);
		return $rez;
	}

}