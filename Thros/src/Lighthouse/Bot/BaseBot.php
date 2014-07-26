<?php

namespace Lighthouse\Bot;

use Lighthouse\Command\Controller;

abstract class BaseBot implements Bot {

	private $_controller = null;

	public function __construct(Controller $controller) {
		$this->_controller = $controller;
	}

	protected function getController() {
		return $this->_controller;
	}

}
