<?php

namespace Deployer\Commands;

class InstallPlugin {

	public $repository;
	public $branch;
	public $type;
	public $private;
	public $ptd;
	public $dryRun;
	public $subdirectory;

	public function __construct( $input ) {
		$this->repository   = $input['repository'];
		$this->branch       = ( isset( $input['branch'] ) ) ? $input['branch'] : '';
		$this->type         = $input['type'];
		$this->private      = ( isset( $input['private'] ) ) ? '1' : '0';
		$this->ptd          = ( isset( $input['ptd'] ) ) ? '1' : '0';
		$this->dryRun       = ( isset( $input['dry-run'] ) ) ? '1' : '0';
		$this->subdirectory = ( isset( $input['subdirectory'] ) ) ? $input['subdirectory'] : null;
	}
}
