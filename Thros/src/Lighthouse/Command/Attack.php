<?php

namespace Lighthouse\Command;

class Attack implements Command {

	private $_energy = null;

	public function __construct($energy) {
		$this->_energy = $energy;
	}

	public function get() {
		return array('command' => 'attack', 'energy' => (int)$this->_energy);
	}

}
