<?php

namespace Deployer\Actions;

use Deployer\Theme;

class ThemeWasEdited {

	/**
	 * @var Theme
	 */
	public $theme;

	public function __construct( Theme $theme ) {
		$this->theme = $theme;
	}
}
