<?php

namespace Lighthouse\Task;

use Lighthouse\Command\Controller;
use Lighthouse\Protocol\StartMessage;
use Lighthouse\Protocol\TurnMessage;

interface Task {

	public function next(Controller $controller, &$tasks, StartMessage $start, TurnMessage $turn);
	public function hasFinished();

}
