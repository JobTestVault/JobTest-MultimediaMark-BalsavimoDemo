<?php

class CheckWebUrlControl
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

	public function update($url='') {
		if ($url != '') { 
			$ch = curl_init();
			$url .= 'index.php?core_func=check';
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_NOBODY, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 3); 
			$data = curl_exec($ch);
			curl_close($ch);
			preg_match_all("/HTTP\/1\.[1|0]\s(\d{3})/",$data,$matches);
			$code = end($matches[1]);
			$this->is_ok = ($code == 200 || $code == 401);
			if ($this->is_ok) {
				$parts = explode(sha1(CFG_INCLUDES_PATH), $data);
				$this->is_ok = (count($parts) == 2);
			}
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