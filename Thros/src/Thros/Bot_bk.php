<?php

namespace Thros;

use \AStar\HeuristicCostCalculator;
use \AStar\Map;
use \AStar\Neighbors\NeighborsFree;

use \Lighthouse\Protocol\StartMessage;
use \Lighthouse\Protocol\TurnMessage;

use \Lighthouse\Command\Attack;
use \Lighthouse\Command\Name;
use \Lighthouse\Command\Connect;
use \Lighthouse\Command\Move;
use \Lighthouse\Command\Pass;

use \Lighthouse\Bot\BaseBot;

class Bot extends BaseBot {

	private $_a_star = null;

	private $_last_status = Status::START;

	public function __construct(\AStar\Engine $aStar, $in, $out, $err = null) {
		parent::__construct($in, $out, $err);

		$this->_a_star = $aStar;
	}

	public function fight() {

		$start = new StartMessage($this->read());
		//$this->debug(print_r($start, true));

		$this->write(new Name(__NAMESPACE__));

		$pass = new Pass();
		while($input = $this->read()) {
			//$this->debug($input, true);
			$turn = new TurnMessage($input);

			if($this->_last_status == Status::START) {
				$path = $this->_findNearestLighthouseNotMine($start, $turn);
				// Lighthouse available => move to the nearest
				if($path) {
					$step = array_shift($path);
					$this->write(new Move($step->getX() - $turn->getX(), $step->getY() - $turn->getY()));
					$this->debug(sprintf('Move %d,%d -> %d,%d', $turn->getX(), $turn->getY(), $step->getX(), $step->getY()));
					$this->_last_status = Status::MOVE;
				}
				else {
					$this->write($pass);
					$this->debug('Pass');
					$this->_last_status = Status::PASSED;
				}
			}
			else if($this->_last_status == Status::MOVE) {
				$step = array_shift($path);
				// Move available
				if($step) {
					$this->write(new Move($step->getX() - $turn->getX(), $step->getY() - $turn->getY()));
					$this->debug(sprintf('Move %d,%d -> %d,%d', $turn->getX(), $turn->getY(), $step->getX(), $step->getY()));
				}
				// On target
				else {
					$lh = $this->_getLighthouseOnPosition($turn);
					// My lighthouse => reload
					if($lh['owner'] == $start->getPlayerNum()) {
						$energy = min($turn->getEnergy(), 100 - $lh['energy']);
					}
					else {
						// Others => attack!!
						if($lh['energy'] > 0) {
							$energy = min($turn->getEnergy(), $lh['energy']);
						}
						// Neutral => gain control
						else {
							$energy = $turn->getEnergy();
						}
					}
					$this->write(new Attack($energy));
					$this->debug(sprintf('Attack %d', $energy));
					$this->_last_status = Status::ATTACK;
				}
			}
			else if($this->_last_status == Status::ATTACK) {
				// Lighthouse is mine and exist one not linked => link
				$lh = $this->_getLighthouseOnPosition($turn);
				if($lh) {
					$lhs = $this->_getLighthousesOf($turn, $start->getPlayerNum());
					$done = false;
					if(count($lhs) > 0) {
						$available = array();
						foreach($lhs as $target) {
							if($target['position'][0] != $turn->getX() && $target['position'][1] != $turn->getY() && $target['have_key']) {
								$available[] = $target;
							}
						}

						if(count($available) > 0) {
							$to = $available[mt_rand(0, count($available) - 1)];
							$this->write(new Connect($to['position'][0], $to['position'][1]));
							$this->debug(sprintf('Connect %d,%d -> %d,%d', $turn->getX(), $turn->getY(), $to['position'][0], $to['position'][1]));
							$this->_last_status = Status::START;
						}
						else {
							$this->write($pass);
							$this->debug('Pass');
							$this->_last_status = Status::PASSED;
						}
					}
					else {
						$this->write($pass);
						$this->debug('Pass');
						$this->_last_status = Status::PASSED;
					}
					/*
					$targets = array();
					foreach($lhs as $target) {
						if($lhs['position'][0] == $turn->getX())
					}
					*/
				}
				else {
					$this->write($pass);
					$this->debug('Pass');
					$this->_last_status = Status::PASSED;
				}
			}
			else if($this->_last_status == Status::PASSED) {
				$path = $this->_findNearestLighthouseNotMine($start, $turn);
				// Lighthouse available => move to the nearest
				if($path) {
					$step = array_shift($path);
					$this->write(new Move($step->getX() - $turn->getX(), $step->getY() - $turn->getY()));
					$this->debug(sprintf('Move %d,%d -> %d,%d', $turn->getX(), $turn->getY(), $step->getX(), $step->getY()));
					$this->_last_status = Status::MOVE;
				}
				else {
					$this->write($pass);
					$this->debug('Pass');
					$this->_last_status = Status::PASSED;
				}
			}
			else {
				$this->write($pass);
				$this->debug('Pass');
				$this->_last_status = Status::PASSED;
			}


			$result = $this->read();
			$this->debug($result);

		}
	}

	private function _findNearestLighthouseNotMine(StartMessage $start, TurnMessage $turn) {
		$min = 1000;
		$target = array();

		$mapData = $start->getMap();
		$width = count($mapData);
		$height = count($mapData[0]);
		foreach($turn->getLighthouses() as $lh) {
			//if($lh['owner'] === $start->getPlayerNum() || ($turn->getX() == $lh['position'][0] && $turn->getY() == $lh['position'][1])) {
			if(($turn->getX() == $lh['position'][0] && $turn->getY() == $lh['position'][1])) {
				continue;
			}

			$map = new Map($width, $height);
			foreach($mapData as $row => $colData) {
				foreach($colData as $col => $value) {
					if($value == 0) {
						$map->set($col, $row, '#');
					}
				}
			}

			$map->set($turn->getX(), $turn->getY(), 'S');
			$map->set($lh['position'][0], $lh['position'][1], 'X');

			$result = $this->_a_star->start($map);
			$count = count($result);
			if($count > 0 && $count < $min) {
				$min = $count;
				$target = array($result);
			}
			else if($count == $min) {
				$target[] = $result;
			}
			//$this->debug(sprintf('(%d,%d) -> (%d,%d) => %d', $turn->getX(), $turn->getY(), $lighthouse[0], $lighthouse[1], count($result)));
		}

		//$this->debug('');
		if(count($target)) {
			return $target[mt_rand(0, count($target) - 1)];
		}
	}

	private function _getLighthouseOnPosition(TurnMessage $turn) {
		foreach($turn->getLighthouses() as $lh) {
			if($lh['position'][0] == $turn->getX() && $lh['position'][1] == $turn->getY()) {
				return $lh;
			}
		}

		return null;
	}

	private function _getLighthousesOf(TurnMessage $turn, $id) {
		$lhs = array();

		foreach($turn->getLighthouses() as $lh) {
			if($lh['owner'] == $id) {
				$lhs[] = $lh;
			}
		}

		return $lhs;
	}

}
