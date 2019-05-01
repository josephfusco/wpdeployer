<?php

namespace Deployer\Commands;

class UnlinkPlugin {

	public $file;

	public function __construct( $input ) {
		$this->file = $input['file'];
	}
}
