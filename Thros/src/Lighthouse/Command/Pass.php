<?php

namespace Lighthouse\Command;

class Pass implements Command {

	public function get() {
		return array('command' => 'pass');
	}

}
