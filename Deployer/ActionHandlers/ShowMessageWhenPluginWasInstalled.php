<?php

namespace Deployer\ActionHandlers;

use Deployer\Actions\PluginWasInstalled;
use Deployer\Dashboard;

class ShowMessageWhenPluginWasInstalled {

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

	/**
	 * @param PluginWasInstalled $action
	 */
	public function handle( PluginWasInstalled $action ) {
		$baseAdminUrl   = ( is_multisite() ) ? network_admin_url() : get_admin_url();
		$activationLink = $baseAdminUrl
			. 'plugins.php?action=activate&plugin='
			. urlencode( $action->plugin->file )
			. '&_wpnonce='
			. wp_create_nonce( 'activate-plugin_' . $action->plugin->file );

		$this->dashboard->addMessage( "Plugin was successfully installed. Go ahead and <a href=\"{$activationLink}\">activate</a> it." );
	}
}
