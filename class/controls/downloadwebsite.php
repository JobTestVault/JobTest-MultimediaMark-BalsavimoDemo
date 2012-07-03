<?php

class DownloadWebsiteControl
	extends Control {

	private $mode = 'normal';
	private $file = '';
	
	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		if (file_exists($this->getFileName())) {
			$this->mode = 'try';
			$this->file = CFG_URL . mb_substr($this->getFileName(), mb_strlen(CFG_ROOT_PATH));
//			$this->tryDownload($url);
		}

		$this->setVar($vars);
	}

	public function beginDownload() {
		$this->mode = 'begin';
		parent::update();
	}

	public function tryDownload($file = '') {
		$this->mode = 'try';
		$this->file = $file;
		parent::update();
	}

	private function getFiles($path) {
		$rez = array();
		if ($dh = opendir($path)) {
			while (($file = readdir($dh)) !== false) {
				if (substr($file, 0, 1) == '.') continue;
				$full_path = $path . $file;
				if (mb_substr($full_path, 0, mb_strlen(CFG_CACHE_PATH) - 1) == mb_substr(CFG_CACHE_PATH, 0, -1) ) continue;
				if ($full_path == CFG_ROOT_PATH . 'config.php') continue;
				if (is_dir($full_path)) {
					$r2 = $this->getFiles($full_path . '/');
					foreach ($r2 as $file) {
						$rez[] = $file;
					}
				} else {
					$rez[] = $full_path;
				}
	        }
		    closedir($dh);
	    }
		return $rez;
	}

	private function getFileName() {
		$session = &Session::getInstance();
		return CFG_CACHE_PATH . 'Balsavimas-WebSite-' . $session->getID()  . '.zip';
	}

	public function makeArchive(&$data, &$msg, &$js) {
		$session = &Session::getInstance();
		$name = 'control_download_website_' . $this->getVar('id') . '_step';
		$step = $session->getVar($name);
		$params = array();
		if (!isset($step['name'])) $step = array('name' => 'begin');
		$next_step = $step['name'];
		switch ($step['name']) {
			case 'begin':
				$next_step = 'clear_unused_tmp_files';
				$msg[] = 'Trinami nenaudingi failai...';
			break;
			case 'clear_unused_tmp_files':
				if ($dh = opendir(CFG_CACHE_PATH)) {
					while (($file = readdir($dh)) !== false) {
						$full_path = CFG_CACHE_PATH . '/' . $file;
						if (is_file($full_path) && (filemtime($full_path) < time() - 3600 * 24)) {
							@unlink($full_path);
						}
					}
					closedir($dh);
				}
				$next_step = 'create_tmp_file';
				$msg[] = 'Sukūriamas archyvas...';
			break;
			case 'create_tmp_file':
				$filename = $this->getFileName();
				touch($filename);
				@chmod($filename, 0777);
				@chgrp($filename, filegroup(__FILE__));
				@chown($filename, fileowner(__FILE__));
				$params['file'] = $filename;
				$next_step = 'add_config_file';
				$msg[] = 'Pridedamas konfiguracijos failas...';
			break;
			case 'add_config_file':
				$data = '<' . "?php

						 /**
						  * Šį failą galite pasiredaguoti pagal save arba tiesiog leisti, kad būtų automatinio įdiegimo metu sukurtas
						  */

						 define('CFG_ROOT_PATH', dirname(__FILE__) . '/' );
						 define('CFG_CLASS_PATH', CFG_ROOT_PATH . 'class/');
						 define('CFG_CLASS_CONTROLS_PATH', CFG_CLASS_PATH . 'controls/');
						 define('CFG_CLASS_SUPPORT_PATH', CFG_CLASS_PATH . 'support/');
						 define('CFG_CLASS_OBJECTS_PATH', CFG_CLASS_PATH . 'objects/');
						 define('CFG_CLASS_KERNEL_PATH', CFG_CLASS_PATH . 'kernel/');
						 define('CFG_CLASS_ACTIONS_PATH', CFG_CLASS_PATH . 'actions/');
						 define('CFG_LIB_PATH', CFG_ROOT_PATH . 'lib/');
						 define('CFG_INCLUDES_PATH', CFG_ROOT_PATH . 'include/');
						 define('CFG_TEMPLATE_PATH', CFG_ROOT_PATH . 'template/');
						 define('CFG_CACHE_PATH', CFG_ROOT_PATH . 'cache/');
						 \$url = (!empty(\$_SERVER['HTTPS'])) ? 'https://'.\$_SERVER['SERVER_NAME'].\$_SERVER['REQUEST_URI'] : 'http://'.\$_SERVER['SERVER_NAME'].\$_SERVER['REQUEST_URI'];
						 define('CFG_URL',  \$url);
						 define('CFG_DB_HOST', 'localhost');
						 define('CFG_DB_USER', '');
						 define('CFG_DB_PASS', '');
						 define('CFG_DB_NAME', '');
						 define('CFG_DB_TYPE', 'mysql');
						 define('CFG_DB_PREFIX', '');";
				file_put_contents(CFG_CACHE_PATH . 'config.php', str_replace(array("\t", "  "), '', $data));
				$params['file'] = $step['params']['file'];
				define( 'PCLZIP_TEMPORARY_DIR', CFG_CACHE_PATH );
				require_once CFG_LIB_PATH . 'pclzip/pclzip.lib.php';
				$zip = new PclZip($params['file']);
				$v_list = $zip->create(CFG_CACHE_PATH . 'config.php', PCLZIP_OPT_REMOVE_PATH, CFG_CACHE_PATH);
				if ($v_list == 0) {
					$msg[] = 'Klaida: Nepavyko pridėti į archyvą :(';
					$next_step = 'err';
				} else {
					@unlink(CFG_CACHE_PATH . 'config.php');
					$next_step = 'add_file';
					$msg[] = 'Pridedami failai...';
					$params['files'] = $this->getFiles(CFG_ROOT_PATH);
				}
			break;
			case 'add_file':
				$params['file'] = $step['params']['file'];
				$params['files'] = $step['params']['files'];
				if (count($params['files']) > 0) {
					define( 'PCLZIP_TEMPORARY_DIR', CFG_CACHE_PATH );
					require_once CFG_LIB_PATH . 'pclzip/pclzip.lib.php';
					$zip = new PclZip($params['file']);
					$mcount = rand(1, 21);
					if ($mcount > count($params['files'])) {
						$mcount = count($params['files']);
					}
					for ($i=0; $i<$mcount; $i++) {
						$cfile = current($params['files']);
						$v_list = $zip->add($cfile, PCLZIP_OPT_REMOVE_PATH, CFG_ROOT_PATH);
						if ($v_list == 0) {
							$msg[] = sprintf('  Nepavyko pridėti „%s“ į archyvą.', mb_substr($cfile, mb_strlen(CFG_ROOT_PATH)) );
							$msg[] = '    ' . $zip->errorInfo(true);
							$next_step = 'err';
							break;
						} else {
							$next_step = 'add_file';
							$msg[] = sprintf('  Pridėtas „%s“ failas.', mb_substr($cfile, mb_strlen(CFG_ROOT_PATH)) );
							unset($params['files'][key($params['files'])]);
						}
					}
					$count = count($params['files']);
					if ($next_step != 'err' && $count > 0) {
						if (($count > 9 && $count < 20) || ($count % 10 == 0) ) {
							$ext = 'ų';
						} elseif ($count % 10 == 1) {
							$ext = 'ą';
						} else {
							$ext = 'us';
						}
						$msg[] = sprintf('  Liko pridėti %d fail%s.',  $count, $ext);
					}
				} else {
					$next_step = 'add_cache_folder';
					$msg[] = 'Pridedamas kešavimo katalogas...';
				}
			break;
			case 'add_cache_folder':
				$params['file'] = $step['params']['file'];
				define( 'PCLZIP_TEMPORARY_DIR', CFG_CACHE_PATH );
				require_once CFG_LIB_PATH . 'pclzip/pclzip.lib.php';
				$zip = new PclZip($params['file']);
				$cfile = CFG_CACHE_PATH . 'cache';
				@mkdir($cfile);
				@touch($cfile . '/index.html');
				$v_list = $zip->add(CFG_CACHE_PATH . 'cache', PCLZIP_OPT_REMOVE_PATH, CFG_CACHE_PATH);
				@unlink($cfile . '/index.html');
				@rmdir($cfile);
				if ($v_list == 0) {
					$msg[] = sprintf('  Nepavyko pridėti „%s“ į archyvą.', mb_substr($cfile, mb_strlen(CFG_CACHE_PATH)) );
					$msg[] = '    ' . $zip->errorInfo(true);
					$next_step = 'err';
				} else {
					$next_step = 'finish';
					$msg[] = sprintf('  Pridėtas „%s“ katalogas.', mb_substr($cfile, mb_strlen(CFG_CACHE_PATH)) );
				}
			break;
			case 'err':
			break;
			case 'finish':
				$msg[] = 'Pabaigtas archyvo sukūrimas!';
				$next_step = 'end';
				$params['file'] = $step['params']['file'];
				$params['file'] = CFG_URL . mb_substr($params['file'], mb_strlen(CFG_ROOT_PATH));
				$js[] = 'window.' . $this->getVar('id') . '.execFunc(\'tryDownload\', [\''.str_replace('\\','/', $params['file']).'\']);';
			break;
			case 'end':
				$params['file'] = $step['params']['file'];
			break;
		}
		$session->setVar($name, array('name' => $next_step, 'params' => $params));
	}

	public function toArray() {
		static $rez = null;
		if ($rez == null) {
			$rez = parent::toArray();
			$rez['mode'] = $this->mode;			
			$rez['file'] = $this->file;
		}
		return $rez;
	}

}