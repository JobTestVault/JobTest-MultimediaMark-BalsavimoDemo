<?php

class InstallAction
	extends Action {

	public function saveConfig($dbtype,$dbname,$dbuser,$dbpass,$dbhost,$web_url) {
		 $root_path = str_replace('\\','/', dirname(dirname(dirname(__FILE__)))) . '/';
		 $cache_path =  $root_path . 'cache/';
		 copy($root_path . 'config.php', $cache_path . 'config.php');
		 if (!$handle = fopen($root_path . 'config.php', 'w')) {
			return false;
		 }
		 $code = 'define(\'%s\', \'%s\');'. "\r\n";
		 fwrite($handle, '<' . '?php'."\r\n\r\n");
		 fwrite($handle, sprintf($code, 'CFG_ROOT_PATH', $root_path));
		 fwrite($handle, sprintf($code, 'CFG_CLASS_PATH', $root_path . 'class/'));
		 fwrite($handle, sprintf($code, 'CFG_CLASS_CONTROLS_PATH', $root_path . 'class/controls/'));
		 fwrite($handle, sprintf($code, 'CFG_CLASS_SUPPORT_PATH', $root_path . 'class/support/'));
		 fwrite($handle, sprintf($code, 'CFG_CLASS_OBJECTS_PATH', $root_path . 'class/objects/'));
		 fwrite($handle, sprintf($code, 'CFG_CLASS_KERNEL_PATH', $root_path . 'class/kernel/'));
		 fwrite($handle, sprintf($code, 'CFG_CLASS_ACTIONS_PATH', $root_path . 'class/actions/'));
		 fwrite($handle, sprintf($code, 'CFG_LIB_PATH', $root_path . 'lib/'));
		 fwrite($handle, sprintf($code, 'CFG_INCLUDES_PATH', $root_path . 'include/'));
		 fwrite($handle, sprintf($code, 'CFG_TEMPLATE_PATH', $root_path . 'template/'));
		 fwrite($handle, sprintf($code, 'CFG_CACHE_PATH', $cache_path));
		 fwrite($handle, sprintf($code, 'CFG_URL', $web_url));
		 fwrite($handle, sprintf($code, 'CFG_DB_HOST', $dbhost));
		 fwrite($handle, sprintf($code, 'CFG_DB_USER', $dbuser));
		 fwrite($handle, sprintf($code, 'CFG_DB_PASS', $dbpass));
		 fwrite($handle, sprintf($code, 'CFG_DB_NAME', $dbname));
		 fwrite($handle, sprintf($code, 'CFG_DB_TYPE', $dbtype));
		 fwrite($handle, sprintf($code, 'CFG_DB_PREFIX', md5(time())));
		 fclose($handle);
		 return true;
	}

	public function createTables() {
		$dir = opendir(CFG_CLASS_OBJECTS_PATH);
		$db = &Database::getInstance();
		while (false !== ($filename = readdir($dir))) {
			if (!is_file(CFG_CLASS_OBJECTS_PATH . $filename)) continue;
			$i = strrpos($filename, '.');
			if ($i === false) continue;
			$ext = substr($filename, $i);
			if ($ext != '.php') continue;
			$obj_type = substr($filename, 0, $i);
			$handler = new Handler($obj_type);
			$sql = $handler->getCreateTableSQL();
			$db->Execute($sql);
		}
		closedir($dir);
	}

	public function saveUser($username,$password) {
		$handler = new Handler('user');
		$handler->add(array('username' => $username, 'password' => $password));
	}

}