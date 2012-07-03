<?php

class Support {

	private $extentions = array();
	private $methods = array();

	/**
	 * Pakrauna kažkokio objekto papildomų galimybių palaikymą
	 *
	 * @params string $type Palaikomo tipo pavadinimas (kiek nori gali būti tokių parametrų
	 */
	protected function addSupport() {
		$args = func_get_args();
		foreach ($args as $support) {
			$class = ucfirst(strtolower($support)) . 'Support';
			$this->extentions[$support] = new $class();
			$methods = get_class_methods($this->extentions[$support]);
			foreach ($methods as $method) {
				$this->methods[strtolower($method)] = $support;
			}
		}
	}

	/**
	 * Bando įvykdyti kažkokią funkciją
	 *
	 * @param string $name funkcijos pavadinimas
	 * @param array $params parametrai
	 *
	 * @return mixed
	 */
	public function __call($name, $params) {
		$cname = strtolower($name);
		if (isset($this->methods[$cname])) {
			return call_user_func_array(array($this->extentions[$this->methods[$cname]], $name), $params);
		} 
		throw new Exception(sprintf('Klasė nepalaiko %s metodo!', $name));
	}

}