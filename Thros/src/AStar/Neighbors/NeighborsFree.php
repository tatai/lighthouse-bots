<?php
namespace AStar\Neighbors;

use AStar\Direction;
use AStar\Node\Node;

class NeighborsFree implements Neighbors {

	public function get(Node $from, $currentDirection) {

		return array(
			Direction::UP        => 1,
			Direction::RIGHT     => 1,
			Direction::DOWN      => 1,
			Direction::LEFT      => 1,
			Direction::UPLEFT    => 1,
			Direction::UPRIGHT   => 1,
			Direction::DOWNLEFT  => 1,
			Direction::DOWNRIGHT => 1
		);

	}

}
