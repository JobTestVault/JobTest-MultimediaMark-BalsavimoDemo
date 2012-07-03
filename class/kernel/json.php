<?php

class JSON {


	/**
	 * Užkoduoja gražinamą informaciją JSON formatu
	 *
	 * @param mixed $input Tai ką reikia paversti į JSON
	 * @return string 
	 */
	public static function encode($input){
		if (is_string($input)) {
			return '"' . str_replace(array('"', "\n", "\r", "\t"), array('\\"', "\\n", "\\r", "\\t"), $input) . '"';
		} elseif (is_bool($input)) {
			return $input?'true':'false';
		} elseif (is_numeric($input)) {
			return (double)$input;
		} elseif (is_array($input)) {
			if (empty($input)) {
				return '[]';
			} else {
				if (is_numeric(key($input))) {
					$rez = '[';
					foreach ($input as $k => $v) {
						if ($rez != '[') {
							$rez .= ', ';
						}
						$rez .= JSON::encode($v);
					}
					$rez .= ']';
				} else {
					$rez = '{';
					foreach ($input as $k => $v) {
						if ($rez != '{') {
							$rez .= ', ';
						}
						$rez .= JSON::encode($k) . ':' . JSON::encode($v);
					}
					$rez .= '}';
				}
				return $rez;
			}
		} elseif (is_null($input)) {
			return 'null';
		}
	}

}