<?php

namespace Gitrub\Domain\General;

class IteratorWithLastElementSaved extends \IteratorIterator {

	private $last_element = null;

	public function current() {
		$element = parent::current();
		$this->last_element = $element;
		return $element;
	}

	public function getLastElement() {
		return $this->last_element;
	}
}
