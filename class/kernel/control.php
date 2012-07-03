<?php

class Control
	extends Support {

	private $vars = array();
	protected $template;
	protected $name;
	protected $styles = array();
	private $timers = array();
	protected $updateMode = false;

	/**
	 * Klasės konstruktorius
	 *
	 * @param string $name kontrolės vardas
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($name) {
		if (strtolower(substr($name, strlen($name) - strlen('Control'))) == 'control') {
			$this->name = strtolower(substr($name, 0, strlen($name) - strlen('Control')));
		} else {
			$this->name = $name;
		}
		if (isset($vars['template'])) {
			$this->template = $vars['template'];
			unset($vars['template']);
		} else {
			$this->template = 'control_' . strtolower($this->name) . '.html';
		}
		$this->addSupport('properties', 'events', 'content');
		$this->initVar('id', 'string',isset($vars['id'])?$vars['id']:Control::generateId($this->name));
	}

	/**
	 * Užregistruoja taimerį
	 *
	 * @param string $action Veiksmo pavadinimas
	 * @param string $interval Intervalas
	 * @param bool $forever Ar veikia amžinai?
	 * @param string $jsArgsGetter JS funkcijos vardas, kuri gražins reikalingus parametrus  
	 */
	protected function registerTimer($action = 'update', $interval = 500, $forever = true, $jsArgsGetter = null) {
		$this->timers[] = compact('action', 'interval', 'forever', 'jsArgsGetter');
	}

	/**
	 * Sugeneruoja automatinį kontrolės id
	 *
	 * @param string
	 * @return string
	 */
	public static function generateId($control_name) {
		$session = &Session::getInstance();
		$count = intval($session->getVar('controls_count')) + 1;
		$session->setVar('controls_count', $count);
		return 'ctl' . Control::getInfoName($control_name) . $count;
	}

	/**
	 * Gražina informacinį kontrolės vardą
	 *
	 * @param string
	 * @return string
	 */
	public static function getInfoName($control_name) {
		 $lvalue = implode('', array_map('ucfirst', explode('_', strtolower($control_name))));
//		 $lvalue = $lvalue{0} . substr($lvalue, 1);
		 return $lvalue;
	}

	/**
	 * Gražina kontrolės klasės pavadinimą
	 *
	 * @param string
	 * @return string
	 */
	public static function getClassName($control_name) {
		 return ucfirst(Control::getInfoName($control_name)) . 'Control';
	}

    /**
	 * Ši funkcija ištrina kai kuriuos gražinamus masyvo elementus (veikia tik per AJAX užklausas)
	 *
	 * @param array $array
	 */
	public function filterAjaxReturnData(&$array) {
	}

	/**
	 * Ši funkcija, jei nėra perrašyta, naudinga tik tuo, kad jos pagalba galima vykdyti ajax'ines kontrolių užklausas
	 */
	public function update() {
		$this->updateMode = true;
	}

	/**
	 * Ši funkcija gražina ar objekto turinys apsikeitė
	 *
	 * @return bool
	 */
	public function needUpdate() {
		return true;
	}

	public function toArray() {
		$data = parent::toArray();
		$cdata = array();
		foreach ($this->getVarNames() as $name) {
			$cdata[$name] = $this->getVar($name, 'source');
		}
		$data['control_data'] = Encryption::simpleEncode($cdata);
		$data['content'] = $this->getContent();
		$data['control_type'] = $this->name;		
		$data['control_isUpdate'] = $this->updateMode;
		return $data;
	}

	/**
	 * Generuoja kontrolės elemento kodą
	 *
	 * @return string
	 */
	public function render() {
		$data = $this->toArray();
		$code = trim(Template::instanceRender($this->template, $data));
		$styles = '';
		foreach ($this->styles as $name => $value) {
			$styles .= "$name: $value;";
		}
		if ($styles) {
			$styles = " style=\"$styles\"";
		}
		if (!$this->updateMode) {
			$code2 = '<script type="text/javascript">jQuery(function(){';
			$code2 .= sprintf("window.%s = window.Control(%s);", $data['id'], JSON::encode(array('id' => $data['id'], 'control_type' => $data['control_type'], 'control_data' => $data['control_data'])));
			foreach ($this->timers as $timer) {
				$code2 .= sprintf('window.%s.timers.add("%s", %d, %s, %s);', $data['id'], $timer['action'], $timer['interval'], ($timer['forever']?'true':'false'), is_string($timer['jsArgsGetter'])?$timer['jsArgsGetter']:'null' );
			}
			$code2 .= '});</script>';
			$code = '<div id="' . $this->getVar('id', 'source') . '" class="'.Control::getClassName($this->name).'"'.$styles.'>' . $code . '</div>' . $code2;
		} 		
		return $code;
	}

}