<?php

namespace Deployer\Handlers;

use Deployer\Actions\PluginWasUpdated;
use Deployer\Commands\UpdatePlugin as UpdatePluginCommand;
use Deployer\Storage\PluginRepository;
use Deployer\WordPress\PluginUpgrader;

class UpdatePlugin {

	/**
	 * @var PluginRepository
	 */
	private $plugins;

	/**
	 * @var PluginUpgrader
	 */
	private $upgrader;

	/**
	 * @param PluginRepository $plugins
	 * @param PluginUpgrader   $upgrader
	 */
	public function __construct( PluginRepository $plugins, PluginUpgrader $upgrader ) {
		$this->plugins  = $plugins;
		$this->upgrader = $upgrader;
	}

	public function handle( UpdatePluginCommand $command ) {
		$plugin = $this->plugins->deployerPluginFromRepository( $command->repository );

		$this->upgrader->upgradePlugin( $plugin );

		do_action( 'wpdeployer_plugin_was_updated', new PluginWasUpdated( $plugin ) );
	}
}
