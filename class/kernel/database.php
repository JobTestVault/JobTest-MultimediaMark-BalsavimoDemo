<?php

class Database {

	/**
	 * Gražina arba jei reikia sukūria database objekto kopiją atmintyje
	 *
	 * @return Database
	 */
	 public static function getInstance() {
		static $instance;
		if (!$instance) {
			require_once CFG_LIB_PATH . 'adodb_lite/adodb.inc.php';
			$instance = ADONewConnection(CFG_DB_TYPE);
			$instance->Connect(CFG_DB_HOST, CFG_DB_USER, CFG_DB_PASS, CFG_DB_NAME);
			$instance->ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		}
		return $instance;
	 }

	 /**
	  * Prideda prefiksą prie lentelės pavadinimo
	  *
	  * @param string $table_name
	  * @return string
	  */
	  public static function prefix($table_name) {
		 return CFG_DB_PREFIX . '_' . $table_name;
	  }

	  /**
	   * Patikrina, ar tinkami duomenų bazės prisijungimai faile
	   *
	   * @return bool
	   */
	  public static function isSettingsOK() {
		  $db = &Database::getInstance();
		  if ($db->isConnected()) {
			  $handler = new Handler('user');
			  $count = $handler->getCount();
			  return ($count > 0);
		  }
		  return false;
	  }

	 /**
	  * Patikrina ar galima prisijungti
	  *
	  * @param string Duomenų bazės tipas
	  * @param string Duomenų bazės vardas
	  * @param string Duomenų bazės vartotojo vardas
	  * @param string Duomenų bazės vartotojo slaptažodis
	  * @param string Duomenų bazės serveris
	  * @return bool;
	  */
	 public static function canConnect($dbtype, $dbname,$dbuser,$dbpass,$dbhost) {
		require_once CFG_LIB_PATH . 'adodb_lite/adodb.inc.php';
		$instance = ADONewConnection($dbtype);
		return (bool)$instance->Connect($dbhost, $dbuser,$dbpass, $dbname);
	 }

}