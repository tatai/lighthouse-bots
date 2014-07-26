<?php

namespace Lighthouse\Command;

class Name implements Command {

	private $_name = null;

	public function __construct($name) {
		$this->_name = $name;
	}

	public function get() {
		return array('name' => $this->_name);
	}

}
