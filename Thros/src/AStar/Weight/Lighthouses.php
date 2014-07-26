<?php
namespace AStar\Weight;

class Lighthouses implements Weight {

	public function getEmpty() {
		return 2;
	}

	public function getWall() {
		return 1000;
	}

	public function getLighthouse() {
		return 1;
	}

}
