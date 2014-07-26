<?php
namespace AStar\Node;

class NodeList {

	private $_nodes = null;
	private $_counter = null;

	public function __construct() {
		$this->_nodes = array();
		$this->_counter = 0;
	}

	public function add(Node $node, $direction, $hash = null) {
		if(is_null($hash)) {
			$hash = (string)$node;
		}
		if(!isset($this->_nodes[$hash][$direction])) {
			$this->_nodes[$hash][$direction] = $node;
			$this->_counter++;
		}
	}

	public function remove(Node $node, $direction) {
		if(isset($this->_nodes[(string)$node][$direction])) {
			unset($this->_nodes[(string)$node][$direction]);
			$this->_counter--;
		}
	}

	public function exists(Node $node, $direction) {
		return isset($this->_nodes[(string)$node][$direction]);
	}

	public function isEmpty() {
		return ($this->_counter <= 0);
	}

	public function getNodeUsingHash($hash, $direction) {
		return $this->_nodes[$hash][$direction];
	}

	public function getNodeWithLowestF() {
		if($this->isEmpty()) {
			return null;
		}

		$candidate = null;
		$candidateDirection = null;
		foreach($this->_nodes as $nodes) {
			foreach($nodes as $direction => $node) {
				if(is_null($candidate) || $node->getF() < $candidate->getF()) {
					$candidate = $node;
					$candidateDirection = $direction;
				}
			}
		}

		return array($candidate, $candidateDirection);
	}

}
