<?php

namespace Deployer\Commands;

class EditTheme {

	public $stylesheet;
	public $repository;
	public $branch;
	public $status;
	public $pushToDeploy;
	public $subdirectory;

	public function __construct( $input ) {
		$this->stylesheet   = $input['stylesheet'];
		$this->repository   = $input['repository'];
		$this->branch       = $input['branch'];
		$this->status       = ( isset( $input['status'] ) ) ? '1' : '0';
		$this->pushToDeploy = ( isset( $input['ptd'] ) ) ? '1' : '0';
		$this->subdirectory = $input['subdirectory'];
	}
}
