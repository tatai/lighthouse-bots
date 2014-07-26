<?php
namespace AStar;

use AStar\Neighbors\Neighbors;
use AStar\Node\Node;
use AStar\Node\NodeList;

class Engine {

	/**
	 *
	 * @var HeuristicCostCalculator
	 */
	private $_heuristic_cost_calculator = null;

	/**
	 *
	 * @var Neighbors
	 */
	private $_neighbors = null;

	/**
	 *
	 * @var NodeList
	 */
	private $_open = null;

	/**
	 *
	 * @var NodeList
	 */
	private $_path = null;

	private $_closed = null;

	public function __construct(HeuristicCostCalculator $heuristicCostCalculator, Neighbors $neighbors) {
		$this->_heuristic_cost_calculator = $heuristicCostCalculator;
		$this->_neighbors = $neighbors;
	}

	private function _initialize() {
		$this->_open = new NodeList();
		$this->_path = new NodeList();
		$this->_closed = new NodeList();
	}

	public function start(Map $map) {
		$this->_initialize();

		$goal = $map->getEnd();
		$start = $map->getStart();
		$start->setG(0);
		$start->setH($this->_heuristic_cost_calculator->calculate($start, $goal));

		$this->_open->add($start, Direction::STALE);
		$this->_path->add($start, Direction::STALE);

		while(!$this->_open->isEmpty()) {
			// Get the node with the lowest f score
			list($current, $currentDirection) = $this->_open->getNodeWithLowestF();

			//$map->debug($current);

			// If node is the goal, we have finish!
			if($current->isEqual($goal)) {
				return $this->_reconstructPath($this->_path, $goal, $currentDirection, $start);
			}

			// Remove current from open list
			$this->_open->remove($current, $currentDirection);

			$this->_closed->add($current, $current->getDirection());
			foreach($this->_getNeighborNodes($current) as $neighbor) {
				if($this->_closed->exists($neighbor, $neighbor->getDirection())) {
					continue;
				}

				if(!$this->_isValidStep($map, $neighbor)) {
					continue;
				}

				if(!$this->_open->exists($neighbor, $neighbor->getDirection()) || $this->_open->getNodeUsingHash((string)$neighbor, $neighbor->getDirection())->getG() > $neighbor->getG()) {
					$this->_path->add($current, $neighbor->getDirection(), (string)$neighbor);
					$neighbor->setH($this->_heuristic_cost_calculator->calculate($neighbor, $goal));
					$this->_open->add($neighbor, $neighbor->getDirection());
				}
			}
		}

		return null;
	}

	private function _getNeighborNodes(Node $node) {
		$neighbors = array();

		foreach($this->_neighbors->get($node, $node->getDirection()) as $direction => $distance) {
			$new = $this->_createNodeFromMove($node, $direction);
			$new->setG($node->getG() + $distance);
			$neighbors[] = $new;
		}

		return $neighbors;
	}

	private function _createNodeFromMove(Node $node, $direction) {
		$x = $node->getX();
		$y = $node->getY();

		if($direction == Direction::UP) {
			return new Node($x, $y - 1, $direction);
		}
		else if($direction == Direction::LEFT) {
			return new Node($x - 1, $y, $direction);
		}
		else if($direction == Direction::DOWN) {
			return new Node($x, $y + 1, $direction);
		}
		else if($direction == Direction::RIGHT) {
			return new Node($x + 1, $y, $direction);
		}
		else if($direction == Direction::UPLEFT) {
			return new Node($x - 1, $y - 1, $direction);
		}
		else if($direction == Direction::UPRIGHT) {
			return new Node($x + 1, $y - 1, $direction);
		}
		else if($direction == Direction::DOWNLEFT) {
			return new Node($x - 1, $y - 1, $direction);
		}
		else if($direction == Direction::DOWNRIGHT) {
			return new Node($x + 1, $y + 1, $direction);
		}

	}

	private function _isValidStep(Map $map, Node $node) {
		return $map->canStepInto($node->getX(), $node->getY());
	}

	private function _reconstructPath(NodeList $path, Node $from, $fromDirection, Node $to) {
		if($path->exists($from, $fromDirection)) {
			$now = $path->getNodeUsingHash((string)$from, $fromDirection);
			if($now->isEqual($to)) {
				return array($from);
			}
			$p = $this->_reconstructPath($path, $now, $now->getDirection(), $to);
			return array_merge($p, array($from));
		}
		return array($from);
	}

}
