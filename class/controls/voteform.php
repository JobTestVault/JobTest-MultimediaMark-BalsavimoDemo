<?php

class VoteFormControl
	extends Control {

	private $sr = false;

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('mode', 'string', 'auto');

		$this->registerTimer('update', 1000);

		$this->setVar($vars);
	}

	/**
	 * Balsuoja
	 * 
	 * @param array $params Kreipimosi duomenys
	 * @param array $error Jei buvo kokių klaidų išsaugome jas čia
	 * @param array $js JavaScript, kuris bus vykdomas po viso šio kodo įvykdymo
	 */
	public function vote($params, &$errors, &$js) {
		if (!isset($params['choice']) || !isset($params['question_id'])) {
			$errors[] = 'Kažkas blogai su siunčiamais duomenimis!';
		} elseif (intval($params['question_id']['value']) == 0) {
			$errors[] = 'Negalima balsuoji nesamoje apklausoje!';
		} else {
			$vote = &$this->getVote();
			$qid = intval(isset($vote['question_id'])?$vote['question_id']:0);
			$cid = intval($params['question_id']['value']);
			if ($qid != $cid) {
				$errors[] = 'Šioje apklausoje jau nebegalima balsuoti. <br /> Prašome atnaujinti puslapį.!';
			} else {
				$handler = new Handler('vote');
				$ip = $this->getIP();
				if ($this->isAlreadyVoted()) {
					$errors[] = 'Jūs jau balsavote šioje apklausoje!';
				} else {
					$vote = &$handler->create();
					$vote->setVar('ip', $ip);
					$vote->setVar('question_id', $cid);
					$vote->setVar('answer_id', $params['choice']['value']);
					$vote->save();
					$js[] = 'window.'.$this->getVar('id').'.execFunc(\'show_results\');';
				}
			}
		}		
	}

	private function isAlreadyVoted() {
		$ip = $this->getIP();
		$handler = new Handler('vote');
		$vote = &$this->getVote();
		$qid = intval(isset($vote['question_id'])?$vote['question_id']:0);
		$objs = &$handler->getObjects(array('question_id' => $qid, 'ip' => $ip));
		return (isset($objs[0]) && is_object($objs[0]));
	}


	private function getIP() {
		return ((isset($_SERVER['HTTP_X_FORWARD_FOR']) && !empty($_SERVER['HTTP_X_FORWARD_FOR']))?$_SERVER['HTTP_X_FORWARD_FOR']:$_SERVER['REMOTE_ADDR']);
	}

	private function getVote() {
		static $data = null;
		if ($data === null) {
			$handler = new Handler('question');
			$objs = $handler->getObjects(array('active' => 1));
			if (isset($objs[0]) && is_object($objs[0])) {
				$data = $objs[0]->toArray();
			} else {
				$data = array();
			}	
		}
		return $data;
	}

	public function show_results() {
		$this->sr = true;
		$this->setVar('mode', 'results');
		parent::update();
	}

	public function needUpdate() {
		$vote = &$this->getVote();
		$qid = isset($vote['question_id'])?$vote['question_id']:0;
		$session = &Session::getInstance();
		if (intval($session->getTempVar('control_vote_form_question_id')) != intval($qid)) {
			$session->setTempVar('control_vote_form_question_id', intval($qid));
			return true;
		}
		return ($this->getVar('mode') == 'results');
	}

	public function toArray() {	
		$rez = parent::toArray();
		$rez['vote'] = &$this->getVote();
		$rez['showResults'] = $this->isAlreadyVoted() || $this->sr || ($this->getVar('mode') == 'results');
		return $rez;
	}

}