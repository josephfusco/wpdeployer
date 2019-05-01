<?php

namespace Deployer\ActionHandlers;

use Deployer\Actions\PluginWasInstalled;
use Deployer\Log\Logger;

class LogWhenPluginWasInstalled {

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
	 * @param PluginWasInstalled $action
	 */
	public function handle( PluginWasInstalled $action ) {
		$this->log->info(
			"Plugin '{name}' was successfully installed. File: '{file}'",
			[
				'name' => $action->plugin->name,
				'file' => $action->plugin->file,
			]
		);
	}
}
