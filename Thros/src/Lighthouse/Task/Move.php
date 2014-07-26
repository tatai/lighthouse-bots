<?php

namespace Lighthouse\Task;

use Lighthouse\Command\Controller;
use Lighthouse\Protocol\StartMessage;
use Lighthouse\Protocol\TurnMessage;

use Lighthouse\Task\FollowPath;

class Move implements Task {

	private $_a_star = null;
	private $_x = null;
	private $_y = null;

	public function __construct($aStar, $x, $y) {
		$this->_a_star = $aStar;
		$this->_x = $x;
		$this->_y = $y;
	}

	public function next(Controller $controller, &$tasks, StartMessage $start, TurnMessage $turn) {
		$mapData = $start->getMap();
		$width = count($mapData);
		$height = count($mapData[0]);

		$map = new \AStar\Map($width, $height);
		foreach($mapData as $row => $colData) {
			foreach($colData as $col => $value) {
				if($value == 0) {
					$map->set($col, $row, '#');
				}
			}
		}

		$map->set($turn->getX(), $turn->getY(), 'S');
		$map->set($this->_x, $this->_y, 'X');

		$result = $this->_a_star->start($map);
		$path = new FollowPath($result);
		array_unshift($tasks, $path);

		$path->next($controller, $tasks, $start, $turn);
	}

	public function hasFinished() {
		return true;
	}

}
