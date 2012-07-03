<?php

class Handler {

	protected $type;
	private $class;
	private $table_name;
	private $dummy_obj;
	private $prefixed_table_name;

   /**
	* Sukūria objektą
	*
	* @param $type tipas
	*/
	function __construct($type) {
		$this->type = $type;
		$this->class = ucfirst(strtolower($type)) . 'Object';
		$this->dummy_obj = &$this->create();
		$this->table_name = $this->dummy_obj->getTableName(false);
		$this->prefixed_table_name = $this->dummy_obj->getTableName(true);
	}

	/**
	 * Gražina lentelės pavadinimą
	 *
	 * @param bool $returnWithPrefix Ar gražinti su prefiksu?
	 * @return string
	 */
	public function getTableName($returnWithPrefix = true) {
		return $returnWithPrefix?$this->prefixed_table_name:$this->table_name;
	}

	/**
	 * Sukūriam naują objektą
	 *
	 * @return Object
	 */
	public function create() {
		$obj = new $this->class();
		return $obj;
	}

	/**
	 * Paiimam objektus iš duomenų bazės
	 *
	 * @param array $filter filtras (naudojame tik objekto savybes)
	 * @param int $from nuo kelinto įrašo skaityti
	 * @param int $count kiek įrašų skaityti
	 * @param string $order rikiavimo tvarka
 	 * @param string $sort rūšiavimo tvarka
	 * @param string $asArray ar gražinti viską kaip paprastą masyvą
	 * @return array
	 */
	 function getObjects($filter = null, $from = null, $count = null, $order = null, $sort = null, $asArray = false) {
		 $db = &Database::getInstance();
		 $sql = 'SELECT * FROM ' . $this->prefixed_table_name;
		 if (is_array($filter) && !empty($filter)) {
			 $sql2 = '';
			 foreach ($filter as $key => $value) {
				 if ($this->dummy_obj->hasVar($key)) {
					if ($sql2 != '') {
						$sql2 .= ' AND ';
					}
					$sql2 .= sprintf('`%s` = "%s" ',$key, $value);
				 } else {
					 throw new Exception(sprintf('Savybės %s objektas neturi!', $key));
				 }
			 } 
			 if ($sql2) {
				 $sql .= ' WHERE ' . $sql2; 
			 }
		 }
		 if (is_numeric($from) && is_numeric($count)) {
			 $sql .= ' LIMIT ' . $from . ',' . $count;
		 } elseif (is_numeric($from)) {
			 $sql .= ' LIMIT ' . $from;
		 } elseif (is_numeric($count)) {
			 $sql .= ' LIMIT 0,' . $count;
		 }
		 if (is_string($order)) {
			 if ($this->dummy_obj->hasVar($order)) {
				 $sql .= ' ORDER BY ' . $order;
				 if (is_string($sort)) {
					 $sort = strtoupper($sort);
					 if (($sort != 'ASC') || ($sort != 'DESC')) {
						  throw new Exception(sprintf('Negalima pagal %s rūšiuoti!', $sort));
					 } else {
						 $sql .= ' ' . $sort;
					 }
				 }
			 } else {
				 throw new Exception(sprintf('Savybės %s objektas neturi!', $order));
			 }
		 }
		 $data = $db->GetArray($sql);
		 $result = array();
		 $field_types = $this->dummy_obj->getFieldTypes();
		 if (is_array($data)) {
			 foreach ($data as $id => $record) {
				 $record_data = array();
				 foreach ($record as  $name => $value) {
					if (isset($field_types[$name])) {
						$record_data[$name] = $value;
					}
				 }
				 $result[] = $asArray?$record_data:new $this->class($record_data);
			 }
		 }
		 return $result;
	 }

	 /** 
	  * Pakeičia visus objektus
	  *
	  * @param array $params
	  * @param array $filter
	  * @return int
	  */
	  public function update($params, $filter = null) {
		  $db = &Database::getInstance();
		  $sql = 'UPDATE `' . $this->prefixed_table_name . '` SET ';
		  foreach ($params as $key => $value) {
			  if ($this->dummy_obj->hasVar($key)) {
				  $sql .= sprintf('`%s` = "%s" ',$key, $value);
			  } else {
				  throw new Exception(sprintf('Savybės %s objektas neturi!', $key));
			  }
		  }
		  if (is_array($filter) && !empty($filter)) {
			 $sql2 = '';
			 foreach ($filter as $key => $value) {
				 if ($this->dummy_obj->hasVar($key)) {
					if ($sql2 != '') {
						$sql2 .= ' AND ';
					}
					$sql2 .= sprintf('`%s` = "%s" ',$key, $value);
				 } else {
					 throw new Exception(sprintf('Savybės %s objektas neturi!', $key));
				 }
			 } 
			 if ($sql2) {
				 $sql .= ' WHERE ' . $sql2; 
			 }
		  }
		  $db->Execute($sql);
		  return $db->Affected_Rows();
	  }

	 /**
	  * Paiimam pirmą objektą
	  *
	  * @param array 
	  * @return object
	  */
	  public function getFirst($filter) {
		  $rez = $this->getObjects($filter, 0, 1);
		  if (isset($rez[0])) {
			  return $rez[0];
		  } 
		  $rez = null;
		  return $rez;
	  }

	  /**
	   * Paiima objektą pagal ID
	   *
	   * @param int $id
	   * @return object
	   */
	  public function get($id) {
		  return $this->getFirst(array($this->dummy_obj->getIDName() => $id));
	  }

	  /**
	   * Prideda elementą duomenų bazėn
	   *
	   * @param array
	   */
	  public function add($data = array()) {
		  $obj = &$this->create();
		  if (!empty($data)) {
			  $obj->setVar($data);
		  }
		  $obj->save();
		  return $obj;
	  }

	  /** 
	   * Gražina kodą, reikalingą sukurti lentelei
	   *
	   * @return string
	   */
	  public function getCreateTableSQL() {
		  $db = &Database::getInstance();		 
		  $sql =  'CREATE TABLE `' . $this->prefixed_table_name . "` (\n";
		  $field_types = $this->dummy_obj->getFieldTypes();
		  $id_name = $this->dummy_obj->getIDName();
		  foreach ($field_types as $name => $type) {
			  $sql .= "\t" . $name;
			  switch ($type) {
				  case 'string':
				  case 'str':
					  $sql .= ' TEXT';
				  break;
				  case 'int':
				  case 'integer':
					  $sql .= ' INT(6)'; 
				  break;
				  case 'bool':
					  $sql .= ' INT(1)';
				  break;
			  }
			  if ($id_name == $name) {
				  $sql .= ' AUTO_INCREMENT PRIMARY KEY';
			  }
			  $sql .= ",\n";
		  }
		  $sql = trim($sql);
		  $sql = substr($sql, 0, strlen($sql) - 1) . "\n";
	      $sql .= ');';
		  return $sql;
	  }

	  /**
	   * Gražina kiek įrašų atitinka kriterijus
	   *
	   * @param array $filter filtras (naudojame tik objekto savybes)
	   * @param int $from nuo kelinto įrašo skaityti
	   * @param int $count kiek įrašų skaityti
	   * @return int
	   */
	  public function getCount($filter = null, $from = null, $count = null) {
		 $result = array();
		 $db = &Database::getInstance();
		 $sql = 'SELECT COUNT(*) FROM ' . $this->prefixed_table_name;
		 if (is_array($filter) && !empty($filter)) {
			 $sql2 = '';
			 foreach ($filter as $key => $value) {
				 if ($this->dummy_obj->hasVar($key)) {
					if ($sql2 != '') {
						$sql2 .= ' AND ';
					}
					if (is_array($value)) {
						$sql2 .= sprintf('`%s` IN ("%s") ', $key, implode('","', $value) );
					} else {
						$sql2 .= sprintf('`%s` = "%s" ',$key, $value);
					}
				 } else {
					 throw new Exception(sprintf('Savybės %s objektas neturi!', $key));
				 }
			 } 
			 if ($sql2) {
				 $sql .= ' WHERE ' . $sql2; 
			 }
		 }
		 if (is_numeric($from) && is_numeric($count)) {
			 $sql .= ' LIMIT ' . $from . ',' . $count;
		 } elseif (is_numeric($from)) {
			 $sql .= ' LIMIT ' . $from;
		 } elseif (is_numeric($count)) {
			 $sql .= ' LIMIT 0,' . $count;
		 }
		 $data = $db->GetArray($sql);
		 if (!$data) return 0;
		 return intval(current($data[0]));
	  }

}