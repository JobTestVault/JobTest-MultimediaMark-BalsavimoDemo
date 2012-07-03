<?php
/**
 * Tai pagrindinis failas, valdantis apklausos veikimą
 */

// Įkrauname nustatymus
require_once dirname(__FILE__) . '/config.php';

// Įkrauname visas reikalingas pagalbines funkcijas
require_once CFG_INCLUDES_PATH . 'functions.php';

// Įkrauname sesiją
$session = &Session::getInstance();

//Apdorojame ir pateikiame rezultatus
$template = &Template::getInstance();
if (isset($_REQUEST['core_func'])) {
	switch ($_REQUEST['core_func']) {
		case 'load_scripts':
			$rez = '';
			foreach ($_REQUEST['file'] as $file) {
				$rez .= file_get_contents($file);
			}
			echo $rez;
			exit(0);
		break;
		case 'check':
			echo time() . sha1(CFG_INCLUDES_PATH) . time();
			exit(0);
		break;
		case 'logout':
			$session->logout();
			$content_file = 'view_login.html';
			$content_title = 'Administravimas';
		break;
	}
} elseif (isset($_POST['control_type']) && isset($_POST['control_action']) && isset($_POST['control_data']) ) {
	$class = Control::getClassName($_POST['control_type']);
	$data = Encryption::simpleDecode($_POST['control_data']);
	$control = new $class($data);
	if ($_POST['control_action'] == 'update' && !$control->needUpdate()) {
		exit(0);
	}
	if (is_callable(array($control, $_POST['control_action']))) {
		if (isset($_POST['control_params']) && is_array($_POST['control_params'])) {
			call_user_func_array(array($control, $_POST['control_action']), $_POST['control_params']);
		} else {
			call_user_func(array($control, $_POST['control_action']));
		}
		$newData = $control->toArray();
		$newData['control_output'] = $control->render();
		$control->filterAjaxReturnData($newData);
	} else {
		$newData = array(
						'err' => 1,
						'control_output' => 'Blogas kreipimasis'
				    );
	}
	echo JSON::encode($newData);
	exit(0);
} elseif (!Database::isSettingsOK()) {
	$content_file = 'view_install.html';
	$content_title = 'Įdiegimas';
} elseif (isset($_GET['area']) && $session->isLoggedIn() && ($_GET['area'] == 'admin') ) {
	if (isset($_GET['action'])) {
		$content_file = 'view_admin_'.urlencode($_GET['action']).'.html';
	} else {
		$content_file = 'view_admin.html';
	}
	$content_title = 'Administravimas';
} elseif (isset($_GET['area']) && !$session->isLoggedIn() && ($_GET['area'] == 'admin') ) {
	$content_file = 'view_login.html';
	$content_title = 'Administravimas';
} else {
	if (!isset($_GET['area']) || empty($_GET['area'])) $_GET['area'] = 'vote';
	$content_file = 'view_'.urlencode($_GET['area']).'.html';
	$content_title = 'Balsavimas';
}
$template->assign('title', $content_title);
/*$template->assign('script_url', CFG_URL.'index.php?core_func=load_scripts&amp;file[]=lib/jquery/jquery.js&amp;file[]=lib/jquery/jquery-ui.js&amp;file[]=lib/jquery/cookie.js&amp;file[]=js/control.js');*/
$template->assign('content', Template::instanceRender($content_file) );
$template->display('global.html');