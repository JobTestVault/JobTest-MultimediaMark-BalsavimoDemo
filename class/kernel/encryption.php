<?php

class Encryption {

	/**
	 * Gražina kodavimo raktą, pagal nutylėjimą
	 *
	 * @return string
	 */
	 public static function getDefaultKey() {
		 $key = str_replace(array(' ','_','/','\\'),'', CFG_CLASS_CONTROLS_PATH);
		 $key = substr($key, 0, round(strlen($key) / 2));
		 return $key;
	 }

	/**
	 * Užšifruoja tekstą paprasčiausiu algoritmu
	 *
	 * @param mixed $text duomenys
	 * @param string $key raktas
	 * @return string
	 */
	 public static function simpleEncode($text, $key = null) {
		 if ($key === null) $key = Encryption::getDefaultKey();
		 $o1 = str_replace('=','',base64_encode(serialize($text)));
		 $o2 = str_replace('=','',base64_encode($key));
		 $chars = md5($key);
		 $rez = $o2.$chars{0}.$o1;
		 $rez = base64_encode($rez);
		 return str_replace('=','',$rez);
	 }

	/**
	 * Atkoduoja tekstą iš paprasčiausio algoritmo
	 *
	 * @param string $text duomenys
	 * @param string $key raktas
	 * @return mixed
	 */
	 public static function simpleDecode($text, $key = null) {
		 if ($key === null) $key = Encryption::getDefaultKey();
		 $o2 = str_replace('=','',base64_encode($key));
		 $o1 = base64_decode($text);
		 $o1 = substr($o1, strlen($o2) + 1);	
		 return unserialize(base64_decode($o1));
	 }

}