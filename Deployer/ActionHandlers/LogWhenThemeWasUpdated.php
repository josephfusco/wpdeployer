<?php

namespace Deployer\ActionHandlers;

use Deployer\Actions\ThemeWasUpdated;
use Deployer\Log\Logger;

class LogWhenThemeWasUpdated {

	/**
	 * @var Logger
	 */
	private $log;

	/**
	 * @param Logger $log
	 */
	public function __construct( Logger $log ) {
		$this->log = $log;
	}

	/**
	 * @param ThemeWasUpdated $action
	 */
	public function handle( ThemeWasUpdated $action ) {
		$this->log->info(
			"Theme '{name}' was successfully updated.",
			[ 'name' => $action->theme->name ]
		);
	}
}
