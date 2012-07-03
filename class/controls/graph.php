<?php

class GraphControl
	extends Control {

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('type', 'string', 'pie');

		$this->setVar($vars);
	}

	public function toArray() {
		$rez = parent::toArray();
		$rez['data'] = array();
		$parts = explode("\n", $rez['content']);
		foreach ($parts as $part) {
			$part = trim($part);
			if ($part == '') continue;
			$i = strrpos($part, ' ');
			$number = intval(trim(mb_substr($part, $i)));
			$title = trim(mb_substr($part, 0, mb_strlen($part) - mb_strlen($number)));
			$rez['data'][] = array($title, $number);
		}
		$rez['data'] = JSON::encode($rez['data']);
		return $rez;
	}

}