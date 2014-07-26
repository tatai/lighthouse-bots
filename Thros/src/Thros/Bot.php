<?php

namespace Thros;

use \AStar\Map;

use \Lighthouse\Protocol\StartMessage;
use \Lighthouse\Protocol\TurnMessage;

use \Lighthouse\Command\Name;
use \Lighthouse\Command\Controller;

use \Lighthouse\Task\FollowPath;
use \Lighthouse\Task\Move;
use \Lighthouse\Task\Attack;
use \Lighthouse\Task\Connect;
use \Lighthouse\Task\RandomCell;

use \Lighthouse\Bot\BaseBot;

class Bot extends BaseBot {

	private $_a_star = null;

	private $_last_status = Status::START;

	private $_tasks = array();

	public function __construct(\AStar\Engine $aStar, Controller $controller) {
		parent::__construct($controller);

		$this->_a_star = $aStar;
	}

	public function fight() {

		$start = new StartMessage($this->getController()->read());

		$mapData = $start->getMap();
		$width = count($mapData);
		$height = count($mapData[0]);
		$cleanMap = new Map($width, $height);
		foreach($mapData as $row => $colData) {
			foreach($colData as $col => $value) {
				if($value == 0) {
					$cleanMap->set($col, $row, '#');
				}
			}
		}

		// Ready!
		$this->getController()->write(new Name(__NAMESPACE__));


		$this->_tasks[] = new RandomCell($this->_a_star);

		while($input = $this->getController()->read()) {

			$turn = new TurnMessage($input);

			if(empty($this->_tasks)) {
				$cf = $this->_targetControlField($cleanMap, $turn);

				$first = array_shift($cf);
				$second = array_shift($cf);
				$third = array_shift($cf);

				$this->_tasks[] = new Move($this->_a_star, $first['lh']->getX(), $first['lh']->getY());
				$this->_tasks[] = new Attack();

				$this->_tasks[] = new Move($this->_a_star, $second['lh']->getX(), $second['lh']->getY());
				$this->_tasks[] = new Attack();
				$this->_tasks[] = new Connect($first['lh']->getX(), $first['lh']->getY());

				$this->_tasks[] = new Move($this->_a_star, $third['lh']->getX(), $third['lh']->getY());
				$this->_tasks[] = new Attack();
				$this->_tasks[] = new Connect($second['lh']->getX(), $second['lh']->getY());

				$this->_tasks[] = new Move($this->_a_star, $first['lh']->getX(), $first['lh']->getY());
				$this->_tasks[] = new Attack();
				$this->_tasks[] = new Connect($third['lh']->getX(), $third['lh']->getY());

				$this->_tasks[] = new RandomCell($this->_a_star);
			}

			// Execute next task
			$step = array_shift($this->_tasks);
			$step->next($this->getController(), $this->_tasks, $start, $turn);
			if(!$step->hasFinished()) {
				array_unshift($this->_tasks, $step);
			}

			// Command result
			$result = $this->getController()->read();
			$this->getController()->debug($result);
		}

	}


	private function _targetControlField(Map $cleanMap, TurnMessage $turn) {
		$cf = array();

		foreach($turn->getLighthouses() as $index => $lh) {
			$map = clone $cleanMap;

			$map->set($turn->getX(), $turn->getY(), 'S');
			$map->set($lh->getX(), $lh->getY(), 'X');

			$result = $this->_a_star->start($map);
			$steps = count($result);

			if($steps > 0) {
				$cf[str_pad($steps, 3, 0, STR_PAD_LEFT) . '-' . str_pad($index, 3, 0, STR_PAD_LEFT)] = array('path' => $result, 'lh' => $lh);
			}
		}

		ksort($cf);

		return array_splice($cf, 0, 3);
	}

}
