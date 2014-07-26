<?php
namespace AStar\Neighbors;

use AStar\Direction;
use AStar\Node\Node;

interface Neighbors {

	public function get(Node $from, $currentDirection);

}
