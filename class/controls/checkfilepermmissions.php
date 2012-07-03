<?php

class CheckFilePermmissionsControl
	extends Control {

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('needed', 'string', 'rw');
		$this->initVar('file', 'string');

		$this->registerTimer();

		$this->setVar($vars);
	}

	/**
	 * Verčiame visa tai į masyvą
	 *
	 * @return string
	 */
	public function toArray() {
		$rez = parent::toArray();
		$rez['is_ok'] = true;
		$modes = $this->getVar('needed', 'source');
		$file = $this->getVar('file', 'source');
		$ic = strlen($modes);
		for($i=0;$i<$ic;$i++) {
			switch ($modes{$i}) {
				case 'r':
					$rez['is_ok'] = $rez['is_ok'] && is_readable($file);
				break;
				case 'w':
					$rez['is_ok'] = $rez['is_ok'] && is_writable($file);
				break;
				case 'f':
					$rez['is_ok'] = $rez['is_ok'] && is_file($file);
				break;
				case 'd':
					$rez['is_ok'] = $rez['is_ok'] && is_dir($file);
				break;
				case 'v':
					$rez['is_ok'] = $rez['is_ok'] && !is_writable($file);
				break;
			}
		}
		return $rez;
	}


}