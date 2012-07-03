<?php

class VoteEditorControl
	extends Control {

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('question_id', 'int');
		$this->initVar('default_answers_count', 'int', 2);

		$this->setVar($vars);
	}

	public function toArray() {
		$rez = parent::toArray();
		$handler = new Handler('question');
		$obj = &$handler->get($this->getVar('question_id'));		
		if (is_object($obj)) {
			$rez['item'] = $obj->toArray();
			$handler2 = new Handler('vote');
			$rez['item']['vote_count'] = $handler2->getCount(array('question_id' => $this->getVar('question_id')));
		} else {
			$count = $this->getVar('default_answers_count');
			$rez['item'] = array('answer' => array());
			for($i=0; $i < $count; $i++) {
				$rez['item']['answer'][] = array('title' => 'Atsakymas Nr.' . strval($i + 1));
			}
			$rez['item']['vote_count'] = 0;
		}
		return $rez;
	}

	/**
	 * Išsaugo 
	 * 
	 * @param array $params Kreipimosi duomenys
	 * @param array $error Jei buvo kokių klaidų išsaugome jas čia
	 */
	public function saveObject($params, &$errors) {
		if (!isset($params['question'])) {
			$errors[] = 'Kažkas blogai su siunčiamais duomenimis!';
		} elseif (trim($params['question']['value']) == '') {
			$errors[] = 'Turi būti įvestas bent koks nors klausimo tekstas!';
		} elseif (count($params) < 3) {
			$errors[] = 'Turi būti bent du klausimo atsakymo variantai!';
		} else {
			$question_title = $params['question']['value'];
			unset($params['question']);
			$answers = array();
			foreach ($params as $param) {
				if (strtolower($param['node']) != 'input') continue;
				$answers[] = $param['value'];
			}
			$handler = new Handler('question');
			$question = &$handler->get($this->getVar('question_id'));
			if (!is_object($question)) {
				$question = &$handler->create();
			} else {
				$question->clearLinkedData();
			}
			$question->setVar('title', $question_title);
			$question->save();
			$handler2 = new Handler('answer');
			$question_id = $question->getID();
			foreach ($answers as $answer) {
				$handler2->add(array('question_id' => $question_id,
									 'title' => $answer));
			}
			$this->setVar('question_id', $question_id);
		}					
	}

}