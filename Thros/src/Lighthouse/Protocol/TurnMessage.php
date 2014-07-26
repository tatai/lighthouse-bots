<?php

namespace Lighthouse\Protocol;

use Lighthouse\Model\LighthouseStatus;

class TurnMessage implements Message {

	private $_message = null;

	private $_lighthouses = null;

	public function __construct($json) {
		$this->_message = json_decode($json, true);
	}

	public function getX() {
		return $this->_message['position'][0];
	}

	public function getY() {
		return $this->_message['position'][1];
	}

	public function getScore() {
		return $this->_message['score'];
	}

	public function getEnergy() {
		return $this->_message['energy'];
	}

	public function getView() {
		return $this->_message['view'];
	}

	/**
	 * 
	 * @return array of LighthouseStatus
	 */
	public function getLighthouses() {
		//return $this->_message['lighthouses'];
		if(is_null($this->_lighthouses)) {
			$this->_lighthouses = array();
			foreach($this->_message['lighthouses'] as $lh) {
				$this->_lighthouses[] = new LighthouseStatus($lh);
			}
		}

		return $this->_lighthouses;
	}

}
