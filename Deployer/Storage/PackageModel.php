<?php

namespace Deployer\Storage;

class PackageModel {

	protected $package;
	protected $repository;
	protected $branch;
	protected $status;
	protected $ptd;
	protected $host;
	protected $private;
	protected $subdirectory;

	public function __construct( array $attributes ) {
		foreach ( $attributes as $key => $value ) {
			$this->$key = sanitize_text_field( $value );
		}
	}

	public function __get( $name ) {
		$method = 'get' . ucfirst( $name );

		if ( method_exists( $this, $method ) ) {
			return $this->$method();
		}

		if ( isset( $this->$name ) ) {
			return $this->$name;
		}
	}
}
