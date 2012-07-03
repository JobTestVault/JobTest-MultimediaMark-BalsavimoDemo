<?php

class Template {

	/**
	 * Gražina arba jei reikia sukūria template objekto kopiją atmintyje
	 *
	 * @return Database
	 */
	 public static function getInstance() {
		require_once CFG_LIB_PATH.'smarty/Smarty.class.php';
		require_once CFG_LIB_PATH.'smarty/cms/compiler.control.php';
		$instance = new Smarty();
		$instance->plugins_dir[] = CFG_LIB_PATH.'smarty/plugins';
		$instance->plugins_dir[] = CFG_LIB_PATH.'smarty/cms';
		$instance->register_compiler_function('/control', 'smarty_compiler_endcontrol');
		$instance->left_delimiter = '<{';
		$instance->right_delimiter = '}>';
		$instance->template_dir = CFG_TEMPLATE_PATH;
		if (is_writable(CFG_CACHE_PATH)) {			
			$instance->cache_dir = CFG_CACHE_PATH;
			$instance->compile_dir = CFG_CACHE_PATH;
//			$instance->caching = true;
		} else {
			$instance->cache_dir = sys_get_temp_dir();
			$instance->compile_dir = sys_get_temp_dir();
//			$instance->caching = false;
		}
		return $instance;
	 }

	 /**
	  * Įkelia kažkokį šabloną ir jį apdoroja
	  *
	  * @param string $template_file
	  * @param array $params
	  * @return string
	  */
	  public static function instanceRender($template_file, $params = array()) {
		  $template = &Template::getInstance();
		  if (!empty($params) && is_array($params)) {
			  $template->assign($params);
		  }
		  return $template->fetch($template_file);
	  }

}