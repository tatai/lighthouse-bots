<?php

namespace Lighthouse\Command;

class Move implements Command {

	private $_x = null;
	private $_y = null;

	public function __construct($x, $y) {
		$this->_x = $x;
		$this->_y = $y;
	}

	public function get() {
		return array('command' => 'move', 'x' => (int)$this->_x, 'y' => (int)$this->_y);
	}

}
