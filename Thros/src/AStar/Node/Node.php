<?php
namespace AStar\Node;

class Node {
	private $_x = null;
	private $_y = null;
	private $_direction = null;

	private $_g = null;
	private $_h = null;

	public function __construct($x, $y, $direction) {
		$this->_x = $x;
		$this->_y = $y;
		$this->_direction = $direction;
	}

	public function getX() {
		return $this->_x;
	}

	public function getY() {
		return $this->_y;
	}

	public function setG($g) {
		$this->_g = $g;

		return $this;
	}

	public function getG() {
		return $this->_g;
	}

	public function setH($h) {
		$this->_h = $h;

		return $this;
	}

	public function getH() {
		return $this->_h;
	}

	public function getF() {
		return $this->getG() + $this->getH();
	}

	public function isEqual(Node $node) {
		return ($this->getX() == $node->getX() && $this->getY() == $node->getY());
	}

	public function getDirection() {
		return $this->_direction;
	}

	public function __toString() {
		return $this->_x . '-' . $this->_y;
	}

}
