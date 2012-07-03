<?php

class Object 
	extends Support {

	protected $table_name, $name;
	private $table_name_with_prefix;
	private $links = array();

	/**
	 * Konstruktorius
	 *
	 * @param string $name Objekto pavadinimas
	 * @param string $values Objekto savybių ir reikšmių masyvas
	 */
	function __construct($name) {
		$this->name = strtolower(str_replace('Object','',$name));
		$this->table_name = $this->name;
		$this->table_name_with_prefix = Database::prefix($this->table_name);
		$this->addSupport('properties', 'events');
		$this->setIDName($this->name . '_id');		
	}

	/**
	 * Nustato pirmines reikšmes
	 *
	 * @param array $vars
	 */
	protected function setDefaultVars($vars = array()) {
		if (!is_array($vars)) $vars = array();
		$this->setVar($vars, true);
		$this->locks[] = 'id';
	}

	/**
	 * Padaro šio objekto ryšį su kitu objektu
	 *
	 * @param string $object_name
	 */
	protected function makeLink($object_name) {
		$this->links[] = $object_name;
	}

	/**
	 * Paiima surištų objektų duomenis
	 *
	 * @param bool $asArray ar gražinti viską kaip masyvą
	 * @return array
	 */
	public function getLinkedData($asArray = false) {
		$data = array();
		foreach ($this->links as $obj_name) {
			$handler = new Handler($obj_name);
			$data[$obj_name] = $handler->getObjects(array($this->getIDName() => $this->getID()), null, null, null, null, $asArray);
		}
		return $data;
	}

	/**
	 * Patrina susijusius duomenis 
	 *
	 * @return int Kiek buvo ištrinta susijusių duomenų
	 */
	public function clearLinkedData() {
		$db = &Database::getInstance();
		$count = 0;
		foreach ($this->links as $obj_name) {
			$handler = new Handler($obj_name);
			$sql = 'DELETE FROM `' . $handler->getTableName() . '` WHERE ' . $this->getIDName() . ' = ' . $this->getID() . ';';
			$db->Execute($sql);
			$count += $db->Affected_Rows();
		}
		return $count;
	}

	/** 
	 * Verčia objektą į masyvą
	 *
	 * @return array
	 */
	 public function toArray() {
		 $rez = parent::toArray();
		 $data = $this->getLinkedData(true);
		 $rez = array_merge($rez, $data);
		 return $rez;
	 }

	/**
	 * Gražina objekto lentelės pavadinimą
	 *
	 * @param bool $withPrefix Ar gražinti jį su prefix'u?
	 * @return string
	 */
	public function getTableName($withPrefix = true) {
		return $withPrefix?$this->table_name_with_prefix:$this->table_name;
	}

	/**
	 * Įrašo objektą į duomenų bazę
	 *
	 * @return bool gražina true, jei pasisekė išsaugoti
	 */
	public function save() {
		$db = &Database::getInstance();
		if ($this->isNew()) {
			$vars = $this->getFieldTypes();
			if (isset($vars[$this->getIDName()])) {
				unset($vars[$this->getIDName()]);
			}
			$sql = 'INSERT INTO `' . $this->getTableName() . '` (`' . implode('`,`', array_keys($vars) ) . '`) VALUES (' ;
			$first = true;
			foreach ($vars as $var => $data) {
				if (!$first) {
					$sql .= ', ';
				} else {
					$first = false;
				}
				$sql .= $this->getVar($var, 'sql');
			}
			$sql .= ');';
			$db->Execute($sql);
			$id = $db->Insert_ID();
			$this->markAsSaved($id);
			return ($db->Affected_Rows() > 0);
		} elseif ($this->wasNotModified()) {
			return false;
		} else {
			$sql = 'UPDATE `' . $this->getTableName() . '` SET '; 
			$first = true;
			$vars = $this->getChangedVars();
			foreach ($vars as $var => $data) {
				if (!$first) {
					$sql .= ', ';
				} else {
					$first = false;
				}
				$sql .= '`' . $var . '`=' . $this->getVar($var, 'sql');				
			}
			$sql .= ' WHERE `' . $this->getIDName() . '` = ' .  $this->getVar($this->getIDName(), 'sql');
			$db->Execute($sql);
			$this->markAsSaved();
			return ($db->Affected_Rows() > 0);
		}				
	}

	/**
	 * Ištrina objektą iš duomenų bazės
	 *
	 * @return bool gražina true, jei pavyko
	 */
	 public function remove() {
		$db = &Database::getInstance();
		$sql = 'DELETE FROM '.$this->getTableName().' WHERE '. $this->getIDName() .' = ' . $this->getVar($this->getIDName(), 'sql');
		$db->Execute($sql);
		if ($db->Affected_Rows() > 0) {
			$data = $this->getLinkedData();
			foreach ($data as $type => $array) {
				foreach ($array as $item) {
					$item->remove();
				}
			}
			return true;
		} else {
			return false;
		}
	 }

}