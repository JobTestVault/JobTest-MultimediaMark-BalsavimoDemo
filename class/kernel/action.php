<?php

class Action {

	/**
	 * Gražina veiksmo klasės pavadinimą
	 *
	 * @param string
	 * @return string
	 */
	public static function getClassName($action_name) {
		 $lvalue = implode('', array_map('ucfirst', explode('_', strtolower($action_name))));
//		 $lvalue = $lvalue{0} . substr($lvalue, 1);
		 return ucfirst($action_name) . 'Action';
	}

}