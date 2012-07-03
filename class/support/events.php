<?php

class EventsSupport {

	private $events = array();

	/**
	 * Užregistruojam įvyko veiksmą
	 *
	 * @param string $action įvykio pavadinimas
	 * @param ref	$function funkcija, kuri turi būti vykdoma įvykus veiksmui
	 */
	 function addHandler($action, $function) {
		if (!isset($this->events[$action])) {
			throw new Exception(sprintf('%s įvykio klasė neturi!', $action));
		} else {
			$this->events[$action][md5(strval($function))] = $function;
		}
	 }

	/**
	 * Vykdo įvykį
	 *
	 * @param string $action veiksmo pavadinimas
	 * @param array $params parametrai
	 */
	 function invoke($action, $params = array()) {
		if (!isset($this->events[$action])) {
			/*throw new Exception(sprintf('%s įvykio klasė neturi!', $action));*/
		} else {
			foreach ($this->events[$action] as $event) {
				call_user_func($event, $params);
			}
		}
	 }

}