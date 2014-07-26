<?php

namespace Lighthouse\Command;

class Controller {

	private $_in = null;
	private $_out = null;
	private $_err = null;

	public function __construct($in, $out, $err = null) {
		$this->_in = $in;
		$this->_out = $out;
		$this->_err = $err;
	}

	public function write(\Lighthouse\Command\Command $command) {
		fwrite($this->_out, json_encode($command->get()) . "\n");
	}

	public function read() {
		return fgets($this->_in);
	}

	public function debug($data) {
		if(!is_null($this->_err)) {
			fwrite($this->_err, $data . "\n");
		}
	}

}