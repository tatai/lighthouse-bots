<?php
namespace AStar;

use AStar\Node\Node;

class HeuristicCostCalculator {

	public function calculate(Node $node, Node $goal) {
		return abs($node->getX() - $goal->getX()) + abs($node->getY() - $goal->getY());
		//return sqrt(pow($node->getX() - $goal->getX(), 2) + pow($node->getY() - $goal->getY(), 2));
	}

}
