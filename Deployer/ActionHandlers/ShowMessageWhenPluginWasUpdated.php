<?php

namespace Deployer\ActionHandlers;

use Deployer\Dashboard;

class ShowMessageWhenPluginWasUpdated {

	/**
	 * @var Dashboard
	 */
	private $dashboard;

	/**
	 * @param Dashboard $dashboard
	 */
	public function __construct( Dashboard $dashboard ) {
		$this->dashboard = $dashboard;
	}

	public function handle() {
		$this->dashboard->addMessage( 'Plugin was successfully updated.' );
	}
}
