<?php

class ListControl
	extends Control {

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);
		$this->initVar('object', 'string');
		$this->initVar('per_page', 'int', 10);
		$this->initVar('page', 'int', 0);
		$this->setVar($vars);

	}

	public function changePage($page = 0) {
		$this->setVar('page', $page);
		parent::update();
	}

	public function toArray() {
		$rez = parent::toArray();		
		$rez['items'] = array();
		$handler = new Handler($this->getVar('object'));
		$dummy_obj = &$handler->create();
		$rez['items']['columns'] = $dummy_obj->getFieldTitles();
		$rez['items']['fields'] = $dummy_obj->getFieldTypes();
		$ppage = $this->getVar('per_page');
		$cpage = ($this->getVar('page') - 1);
		if ($cpage < 0) $cpage = 0;
		$rez['items']['data'] = &$handler->getObjects(array(), $cpage , $ppage, null, null, true);
		$rez['items']['ccount'] = count($rez['items']['columns']);
		$rez['items']['cpage'] = 0;
		$rez['items']['count'] = $handler->getCount();
		$rez['items']['pages'] = array();
		for($i=0; $i<($rez['items']['count']-1); $i+=$ppage) {
			$rez['items']['pages'][] = strval($i+1);
 		}
		if (count($rez['items']['pages']) == 0) {
			$rez['items']['pages'][] = '1';
		}
		$rez['items']['id'] = $dummy_obj->getIDName();
		return $rez;
	}

}