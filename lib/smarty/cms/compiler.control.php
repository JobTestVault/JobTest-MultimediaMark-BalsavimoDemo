<?php

/**
 * User can use controls on the selected theme
 */
function smarty_compiler_control( $tag_arg, &$smarty ) {
	$tag_arg = trim($tag_arg);
	$closed_tag = substr($tag_arg, strlen($tag_arg) - 1, 1) == '/';
	if ($closed_tag) {
		$tag_arg = trim(substr($tag_arg, 0, strlen($tag_arg) - 1));
	}
	$params = $smarty->_parse_attrs($tag_arg);
	if (!isset($params['name'])) {
		$smarty->trigger_error("Control name not set", E_USER_ERROR, __FILE__, __LINE__);
	}
	$params['name'] = str_replace(array('\'','"'), '', strtolower($params['name']));
	if (!isset($params['id']) || empty($params['id']) || strlen(trim($params['id'])) < 1) {		
		$params['id'] = Control::generateId($params['name']);
	} else {
		$truename = $smarty->_dequote($params['id']);
	}
	$name = ucfirst($params['name']);
	$id = $params['id'];
	unset($params['name'], $params['id']);
	$rcode = '';
	foreach($params as $k => $v) {
		$rcode .= " '$k' => ";
		switch ($v{0}) {
			case '@':
			case '$':
			case '\'':
			case '"':
				$rcode .= $v;
			break;
			default:
				$rcode .= "'".addslashes($v)."'";
			break;
		}
		$rcode .= ',';
	}
	if (($name{0} == '$') || ($name{0} == '@')) {
		if (!isset($truename)) {
			$rcode .= ' \'id\'=> Control::generateId('.$name.')';
		} else {
			$rcode .= " 'id'=>".$truename;
		}
		$code = '';	
		$code .= '$class = Control::getClassName('.$name.'); $this->_controls[++$this->_controlsCount] = new $class(array(' .trim($rcode). '));';
	} else {
		if (!isset($truename)) {
			$rcode .= ' \'id\'=> Control::generateId(\''.$name.'\')';
		} else {
			$rcode .= " 'id'=>'".addslashes($truename)."'";
		}
		$code = '';	
		$code .= '$this->_controls[++$this->_controlsCount] = new '.Control::getClassName($name).'(array(' .trim($rcode). '));';

	}
	if ($closed_tag) {
		$code .= 'echo $this->_controls[$this->_controlsCount]->render(); unset($this->_controls[$this->_controlsCount--]);';
	} else {
		$code .= 'ob_start();';
	}
	return $code;
}

function smarty_compiler_endcontrol( $tag_arg, &$smarty ) {
	return '
			$this->_controls[$this->_controlsCount]->setContent(ob_get_contents()); 
			ob_end_clean();
			echo $this->_controls[$this->_controlsCount]->render();			
			unset($this->_controls[$this->_controlsCount--]);';
}

?>