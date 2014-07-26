<?php

namespace Lighthouse\Task;

use Lighthouse\Command\Controller;
use Lighthouse\Protocol\StartMessage;
use Lighthouse\Protocol\TurnMessage;

class Attack implements Task {

	public function next(Controller $controller, &$tasks, StartMessage $start, TurnMessage $turn) {
		$lh = $this->_getLighthouseOnPosition($turn);

		// My lighthouse => reload
		if($lh->getOwner() == $start->getPlayerNum()) {
			//$energy = min($turn->getEnergy(), 100 - $lh->getEnergy());
			$energy = $turn->getEnergy();
		}
		else {
			// Others => attack!!
			if($lh->getEnergy() > 0) {
				//$energy = min($turn->getEnergy(), $lh->getEnergy());
				$energy = $turn->getEnergy();
			}
			// Neutral => gain control
			else {
				$energy = $turn->getEnergy();
			}
		}

		$controller->write(new \Lighthouse\Command\Attack($energy));
		$controller->debug(sprintf('Attack %d', $energy));
	}

	public function hasFinished() {
		return true;
	}

	private function _getLighthouseOnPosition(TurnMessage $turn) {
		foreach($turn->getLighthouses() as $lh) {
			if($lh->getX() == $turn->getX() && $lh->getY() == $turn->getY()) {
				return $lh;
			}
		}

		return null;
	}

}
