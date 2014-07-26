<?php

namespace Lighthouse\Task;

use Lighthouse\Command\Controller;
use Lighthouse\Protocol\StartMessage;
use Lighthouse\Protocol\TurnMessage;

use Lighthouse\Task\FollowPath;

class RandomCell implements Task {

	private $_a_star = null;

	public function __construct($aStar) {
		$this->_a_star = $aStar;
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

		do {
			$x = mt_rand(0, $width - 1);
			$y = mt_rand(0, $height - 1);
		} while($map->get($x, $y) == '#');

		$map->set($turn->getX(), $turn->getY(), 'S');
		$map->set($x, $y, 'X');

		$result = $this->_a_star->start($map);
		$path = new FollowPath($result);
		array_unshift($tasks, $path);

		$path->next($controller, $tasks, $start, $turn);

		/*
		$controller->write(new \Lighthouse\Command\Pass());
		$controller->debug('Pass');
		*/
	}

	public function hasFinished() {
		return true;
	}

}
