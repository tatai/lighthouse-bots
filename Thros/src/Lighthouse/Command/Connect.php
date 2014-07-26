<?php

namespace Lighthouse\Command;

class Connect implements Command {

	private $_x = null;
	private $_y = null;

	public function __construct($x, $y) {
		$this->_x = $x;
		$this->_y = $y;
	}

	public function get() {
		return array('command' => 'connect', 'destination' => array((int)$this->_x, (int)$this->_y));
	}

}
