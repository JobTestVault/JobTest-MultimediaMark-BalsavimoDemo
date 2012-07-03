<?php

function __autoload($class) {
	static $types = array(
				 'Control' => CFG_CLASS_CONTROLS_PATH,
				 'Support' => CFG_CLASS_SUPPORT_PATH,
				 'Object' => CFG_CLASS_OBJECTS_PATH,
				 'Action' => CFG_CLASS_ACTIONS_PATH
		     );
	foreach ( $types as $name => $path) {
		if ((substr($class, strlen($class) - strlen($name)) == $name) && (strlen(strtolower(substr($class, 0, strlen($class) - strlen($name)))) > 0) ) {
			return include($path . strtolower(substr($class, 0, strlen($class) - strlen($name)) . '.php'));			
		}		
	}
	return include(CFG_CLASS_KERNEL_PATH . strtolower($class) . '.php');
}