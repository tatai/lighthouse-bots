<?php
namespace AStar;

use AStar\Node\Node;

class Map {
	private $_width = null;
	private $_height = null;
	private $_empty_char = '.';
	private $_start_char = 'S';
	private $_end_char = 'X';
	private $_wall_char = '#';
	private $_current_char = '%';
	private $_cells = null;

	/**
	 *
	 * @var Node
	 */
	private $_start_position = null;

	/**
	 *
	 * @var Node
	 */
	private $_end_position = null;

	public function __construct($width, $height) {
		$this->_width = $width;
		$this->_height = $height;

		$this->restart();
	}

	public function restart() {
		unset($this->_cells);
		$this->_cells = array_fill(0, $this->_width, str_repeat($this->_empty_char, $this->_height));
	}

	public function set($x, $y, $char) {
		$this->_cells[$x][$y] = $char;

		if($char == $this->_start_char) {
			$this->_start_position = new Node($x, $y, Direction::STALE);
		}
		else if($char == $this->_end_char) {
			$this->_end_position = new Node($x, $y, Direction::STALE);
		}

		return $this;
	}

	public function get($x, $y) {
		return $this->_cells[$x][$y];
	}

	public function canStepInto($x, $y) {
		return ($x >= 0 && $x < $this->_width && $y >= 0 && $y < $this->_height) && ($this->_cells[$x][$y] != $this->_wall_char);
	}

	/**
	 *
	 * @return Node
	 */
	public function getStart() {
		return $this->_start_position;
	}

	/**
	 *
	 * @return Node
	 */
	public function getEnd() {
		return $this->_end_position;
	}

	public function debug(Node $current = null, $debug = null) {
		if(is_null($debug)) {
			return false;
		}

		for($j = 0; $j < $this->_height; $j++) {
			for($i = 0; $i < $this->_width; $i++) {
				if(!is_null($current) && $current->getX() == $i && $current->getY() == $j) {
					fwrite($debug, $this->_current_char);
				}
				else {
					fwrite($debug, $this->_cells[$i][$j]);
				}
			}
			fwrite($debug, "\n");
		}

		fwrite($debug, "\n");
	}

}
