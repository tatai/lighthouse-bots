<?php

namespace Lighthouse\Task;

use Lighthouse\Command\Controller;
use Lighthouse\Protocol\StartMessage;
use Lighthouse\Protocol\TurnMessage;

class FollowPath implements Task {

	private $_steps = null;

	public function __construct($steps) {
		$this->_steps = $steps;
	}

	public function next(Controller $controller, &$tasks, StartMessage $start, TurnMessage $turn) {
		if(!empty($this->_steps) && ($step = array_shift($this->_steps))) {
			$controller->write(new \Lighthouse\Command\Move($step->getX() - $turn->getX(), $step->getY() - $turn->getY()));
			$controller->debug(sprintf('Move %d,%d -> %d,%d', $turn->getX(), $turn->getY(), $step->getX(), $step->getY()));
		}
		else {
			$controller->write(new \Lighthouse\Command\Pass());
			$controller->debug(sprintf('Pass'));
		}
	}

	public function hasFinished() {
		return empty($this->_steps);
	}

}
