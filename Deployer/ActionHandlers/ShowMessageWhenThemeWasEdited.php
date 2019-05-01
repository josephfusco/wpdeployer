<?php

namespace Deployer\ActionHandlers;

use Deployer\Actions\ThemeWasEdited;
use Deployer\Dashboard;

class ShowMessageWhenThemeWasEdited {

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

	public function handle( ThemeWasEdited $action ) {
		$this->dashboard->addMessage( 'Theme changes was successfully saved.' );
	}
}
