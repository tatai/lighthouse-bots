<?php

namespace Lighthouse\Model;

class LighthouseStatus {

	private $_data = null;

	public function __construct($data) {
		$this->_data = $data;
	}

	public function getX() {
		return $this->_data['position'][0];
	}

	public function getY() {
		return $this->_data['position'][1];
	}

	public function getOwner() {
		return $this->_data['owner'];
	}

	public function getEnergy() {
		return $this->_data['energy'];
	}

	public function getHaveKey() {
		return $this->_data['have_key'];
	}

	public function getConnections() {
		return $this->_data['connections'];
	}

}