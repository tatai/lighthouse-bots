<?php

namespace Lighthouse\Protocol;

class StartMessage implements Message {

	private $_message = null;

	public function __construct($json) {
		$this->_message = json_decode($json, true);
	}

	public function getPlayerNum() {
		return $this->_message['player_num'];
	}

	public function getPlayerCount() {
		return $this->_message['player_count'];
	}

	public function getX() {
		return $this->_message['position'][0];
	}

	public function getY() {
		return $this->_message['position'][1];
	}

	public function getMap() {
		return $this->_message['map'];
	}

	public function getLighthouses() {
		return $this->_message['lighthouses'];
	}

}
