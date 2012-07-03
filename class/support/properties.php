<?php

class PropertiesSupport {

	protected $vars = array();
	protected $needSave = false;
	protected $locks = array();
	private $id_name = 'id';

	/**
	 * Nustato ID laukelio vardą
	 *
	 * @param string $id_name
	 */
	public function setIDName($id_name) {
		if (isset($this->vars[$this->id_name])) {
			unset($this->vars[$this->id_name]);
		}
		$this->id_name = $id_name;
		$this->initVar($id_name, 'int');
	}

	/**
	 * Gražina ID laukelio vardą
	 *
	 * @return string
	 */
	public function getIDName() {
		return $this->id_name;
	}

	/**
	 * Gražina ID laukelio turinį
	 *
	 * @return int
	 */
	public function getID() {
		$id = intval($this->vars[$this->id_name]['value']);
		return ($id==0)?null:$id;
	}

	/**
	 * Nurodo, kad objektas turi tam tikrą savybę
	 *
	 * @param string $var savybės pavadinimas
	 * @param string $type tipo pavadinimas
	 * @param mixed $default savybės reikšmė pagal nutylėjimą 
	 * @param string $title išvedamas tekstas
	 */
	public function initVar($var, $type = 'text', $default = null, $title = null) {
		$this->vars[$var] = array('value' => $default, 'type' => $type, 'pvalue' => $default, 'title' => ($title === null)?$var:$title );
	}

	/**
	 * gražina savybės reikšmę
	 *
	 * @param string $var savybės pavadinimas
	 * @param string $type savybės gražinimo būdas
	 *
	 * @return mixed
	 */
	public function getVar($var, $type = 'view') {
		if (!isset($this->vars[$var])) {
			throw new Exception(sprintf('Kintamojo %s klasė neturi!', $var));
		} else {
			switch ($type) {
				case 'view':
					switch ($this->vars[$var]['type']) {
						case 'int':
						case 'integer':
						case 'str':
						case 'string':
							return $this->vars[$var]['value'];
						break;
						case 'bool':
							return $this->vars[$var]['value']?'+':'-';
						break;
					}
				break;
				case 'sql':
					switch ($this->vars[$var]['type']) {
						case 'int':
						case 'integer':
							return strval(intval($this->vars[$var]['value']));
						break;
						case 'str':
						case 'string':
							$db = &Database::getInstance();
							return @$db->Qmagic(strval($this->vars[$var]['value']));
						break;
						case 'bool':
							return $this->vars[$var]['value']?'1':'0';
						break;
					}					
				break;
				default:
					return $this->vars[$var]['value'];
				break;
			}
		}
	}

	/** 
	 * Gražina pasikeitusias savybes kaip masyvą
	 *
	 * @return array
	 */
	public function getChangedVars() {
		$rez = array();
		foreach ($this->vars as $var => $data) {
			if ($data['value'] == $data['pvalue']) continue;
			$rez[$var] = $data['value'];
		}
		return $rez;
	}

	/**
	 * Pakeičia savybės reikšmę
	 *
	 * @param string/array $var savybės pavadinimas arba savybių pavadinimų ir reikšmių masyvas
	 * @param mixed $value reikšmė
	 */
	public function setVar($var, $value = null) {
		if (is_array($var)) {
			foreach($var as $key => $val) {
				$this->setVar($key, $val);
				if ($value == true) {
					$this->vars[$key]['pvalue'] = $this->vars[$key]['value'];
				}
			}
			return;
		} elseif ($var == null) {
			return;
		}		
		if (!isset($this->vars[$var])) {
			throw new Exception(sprintf('Kintamojo %s klasė neturi!', $var));
		} elseif ($var == $this->id_name && in_array('id', $this->locks) ) {
			throw new Exception('Objekto ID reikšmė negali būti keičiama iš išorės!');
		} elseif ($value != $this->vars[$var]['value']) {
			$this->vars[$var]['pvalue'] = $this->vars[$var]['value'];
			switch ($this->vars[$var]['type']) {
				case 'int':
				case 'integer':
					$this->vars[$var]['value'] = intval($value);
				break;
				case 'str':
				case 'string':
					$this->vars[$var]['value'] = strval($value);
				break;
				case 'bool':
					$this->vars[$var]['value'] = (bool)$value;
				break;
			}			
			$this->needSave = true;
		}				
	}

	/**
	 * Gražina savybės pavadinimą
	 * 
	 * @param string $var
	 * @return string
	 */
	public function getVarTitle($var) {
		return $this->vars[$key]['title'];
	}

	/**
	 * Gražina informaciją ar objekto dar nėra duomenų bazėje
	 *
	 * @return bool
	 */
	public function isNew() {
		return ($this->getID() == null);
	}

	/**
	 * Gražina informaciją ar objektas buvo nemodifikuotas
	 *
	 * @return bool
	 */
	public function wasNotModified() {
		return !$this->needSave;
	}

	/**
	 * Gražina visas savybes kaip masyvą
	 *
	 * @return array
	 */
	public function toArray() {
		$rez = array();
		foreach ($this->vars as $key => $value) {
			$rez[$key] = $this->getVar($key, 'source');
		}
		return $rez;
	}

	/**
	 * Gražina savybių pavadinimus
	 *
	 * @return array
	 */
	public function getVarNames() {
		return array_keys($this->vars);
	}

	/**
	 * Patikrina ar yra tokia savybė
	 * 
	 * @param string $name
	 * @return bool
	 */
	 public function hasVar($name) {
		 return isset($this->vars[$name]);
	 }

	 /**
	  * Gražina kintamųjų tipus
	  *
	  * @return array
	  */
	 public function getFieldTypes() {
		$rez = array();
		foreach ($this->vars as $key => $value) {
			$rez[$key] = $value['type'];
		}
		return $rez;
	 }

	 /**
	  * Gražina kintamųjų pavadinimus
	  *
	  * @return array
	  */
	 public function getFieldTitles() {
		$rez = array();
		foreach ($this->vars as $key => $value) {
			$rez[$key] = ucfirst($value['title']);
		}
		return $rez;
	 }

	 /**
	  * Pažymi visus duomenis kaip išsaugotus
	  *
	  * @param int $id
	  */
	 public function markAsSaved($newId = null) {
		foreach ($this->vars as $key => $value) {
			$this->vars[$key]['pvalue'] = $this->vars[$key]['value'];
		}
		if (intval($newId) > 0) {
			$this->vars[$this->id_name]['value'] = $newId;
		}
		$this->needSave = false;
	 }

}