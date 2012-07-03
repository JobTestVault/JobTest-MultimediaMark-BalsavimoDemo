<?php

class VoteResultsControl
	extends Control {

	/**
	 * Klasės konstruktorius
	 *
	 * @param array $vars savybių ir jų reikšmių masyvas
	 */
	public function __construct($vars = array()) {
		parent::__construct(__CLASS__);

		$this->initVar('question_id', 'int');

		$this->registerTimer('update', 5000);

		$this->setVar($vars);
	}


	public function needUpdate() {
		$vote_handler = new Handler('vote');
		$count = $vote_handler->getCount(array('question_id' => $this->getVar('question_id')));
		$session = &Session::getInstance();
		if ($session->getTempVar('control_vote_form_count') != $count) {
			$session->setTempVar('control_vote_form_count', $count);
			return true;
		}
		return false;
	}

	public function toArray() {
		$rez = parent::toArray();
		$vote_handler = new Handler('vote');
		$answers_handler = new Handler('answer');
		$question_handler = new Handler('question');
		$question = &$question_handler->get($this->getVar('question_id'));
		if (is_object($question)) {
			$answers = &$answers_handler->getObjects(array('question_id' => $this->getVar('question_id')));
			$rez['title'] = $question->getVar('title');
			$rez['votes'] = array();
			$rez['count'] = $vote_handler->getCount(array('question_id' => $this->getVar('question_id')));
			foreach ($answers as $answer) {
				$count = ($rez['count'] > 0)?$vote_handler->getCount(array('question_id' => $this->getVar('question_id'), 'answer_id' => $answer->getVar('answer_id'))):0;
				$rez['votes'][] = array(
									'id' => $answer->getID(),
									'title' => $answer->getVar('title'),
									'count' => $count,
									'percent' => ($rez['count']>0)?round(100 / $rez['count'] * $count):0
								 );
			}
			$session = &Session::getInstance();
			$session->setTempVar('control_vote_form_count', $rez['count']);
		} else {
			$rez['noquestion'] = true;
		}
		return $rez;
	}

}