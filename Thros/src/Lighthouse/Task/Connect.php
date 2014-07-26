<?php

namespace Lighthouse\Task;

use Lighthouse\Command\Controller;
use Lighthouse\Protocol\StartMessage;
use Lighthouse\Protocol\TurnMessage;

class Connect implements Task {

	private $_x = null;
	private $_y = null;

	public function __construct($x, $y) {
		$this->_x = $x;
		$this->_y = $y;
	}

	public function next(Controller $controller, &$tasks, StartMessage $start, TurnMessage $turn) {
		$controller->write(new \Lighthouse\Command\Connect($this->_x, $this->_y));
		$controller->debug(sprintf('Connect %d,%d -> %d,%d', $turn->getX(), $turn->getY(), $this->_x, $this->_y));
	}

	public function hasFinished() {
		return true;
	}

}
