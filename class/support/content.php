<?php

class ContentSupport {

	private $content = '';

	/**
	 * Nustato papildomą kontrolės turinį
	 *
	 * @param string $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * Gražina kontrolės saugomą papildomą turinį
	 *
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

}