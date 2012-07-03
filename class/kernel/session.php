<?php

class Session {

	private $user = false;

	/**
	 * Gražina arba jei reikia sukūria database objekto kopiją atmintyje
	 *
	 * @return Database
	 */
	public static function getInstance() {
		static $instance = null;
		if ($instance === null) {
			$instance = new Session();
		}
		return $instance;
	}

	/**
	 * Pradeda sesiją
	 */
	 function __construct() {
		 if (is_writable(CFG_CACHE_PATH)) {
			 session_save_path(CFG_CACHE_PATH);
			 ini_set('session.gc_probability', 1);
		 }
		 session_start();
	 }

	 /**
	  * Užbaigiam sesiją
	  */
	 function __destruct() {
		 session_commit();
	 }

	 /**
	  * Patikrina ar vartotojas prisijungęs
	  *
	  * @return bool
	  */
	 public function isLoggedIn() {
		 return is_object($this->getUser());
	 }

	 /**
	  * Gražina vartotoją
	  *
	  * @return mixed gražina null, jei niekas neprisijungęs, priešingu atveju User objektą
	  */
	 public function getUser() {
		 if ($this->user === false) {
			 if (is_numeric($this->getVar('user_id'))) {
				 $handler = new Handler('user');
				 $this->user = &$handler->getFirst(array('user_id' => $this->getVar('user_id')));
			 }
		 }
		 return $this->user;
	 }

	 /**
	  * Prisijungia... arba ne
	  *
	  * @param string $username Vartotojo vardas
	  * @param string $password Slaptažodis
	  * @return bool Gražina true, jei pasisekė prisijungti ir false, jei ne
	  */
	  public function login($username, $password) {
		  $handler = new Handler('user');
	      $this->user = &$handler->getFirst(array('username' => $username, 'password' => $password));
		  if (is_object($this->user)) {
			  $this->setVar('user_id', $this->user->getID());
			  return true;
		  }
		  return false;
	  }

	  /**
	  * Atsijungia... arba ne
	  *
	  * @return bool Gražina true, jei pasisekė atsijungti ir false, jei ne
	  */
	  public function logout() {
		 if ($this->isLoggedIn()) {
			 session_destroy();
			 return true;
		 } 
		 return false;
	  }

	  /**
	   * Išsaugo kintamąjį sesijoje
	   *
	   * @param string $name
	   * @param mixed $value
	   */
	  public function setVar($name, $value) {
		 $_SESSION[$name] = $value;
	  }

	  /**
	   * Gražina sesijos kintamojo reikšmę
	   *
	   * @param string $name
	   * @return mixed
	   */
	  public function getVar($name) {
		 return isset($_SESSION[$name])?$_SESSION[$name]:null;
	  }

	  /**
	   * Išsaugo laikinąjį kintamąjį sesijoje
	   *
	   * @param string $name
	   * @param mixed $value
	   * @param int $time
	   */
	  public function setTempVar($name, $value, $time = null) {
		 $this->checkTempVars();
		 $_SESSION['__temp_vars'][$name] =  array(
												'value' => $value,
												'time' => is_int($time)?$time:time()+600
											);
	  }

	  /**
	   * Panaikina nerebereikalingus laikinuosius kinatmuosius
	   *
	   * @param string $varName kokį kintąmajį pratęsti
	   */
	  private function checkTempVars($varName = null) {
		 if (isset($_SESSION['__temp_vars']) && is_array($_SESSION['__temp_vars'])) {
			$length = count($_SESSION['__temp_vars']);
			$keys = array_keys($_SESSION['__temp_vars']);
			for($i = 0; $i < $length; $i++) {
				if ($varName == $keys[$i]) continue;
				if ($_SESSION['__temp_vars'][$keys[$i]]['time'] < time()) {
					unset($_SESSION['__temp_vars'][$keys[$i]]);
				}
			}
		 } else {
			$_SESSION['__temp_vars'] = array();
		 }
	  }

	  /**
	   * Gražina sesijos laikinojo kintamojo reikšmę
	   *
	   * @param string $name
	   * @return mixed
	   */
	  public function getTempVar($name) {
		 return isset($_SESSION['__temp_vars'][$name])?$_SESSION['__temp_vars'][$name]['value']:null;
	  }

	  /**
	   * Ištrina sesijos kintamajį
	   * @param string $name
	   */
	  public function removeVar($name) {
		  if (isset($_SESSION[$name])) unset($_SESSION[$name]);
	  }

	  /**
	   * Gražina sesijos ID
	   *
	   * @return string
	   */
	   public function getID() {
		   return session_id();
	   }

}