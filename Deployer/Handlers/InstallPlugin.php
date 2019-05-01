<?php

namespace Deployer\Handlers;

use Deployer\Actions\PluginWasInstalled;
use Deployer\Commands\InstallPlugin as InstallPluginCommand;
use Deployer\Dashboard;
use Deployer\Git\RepositoryFactory;
use Deployer\Log\Logger;
use Deployer\Plugin;
use Deployer\Deployer;
use Deployer\Storage\PluginRepository;
use Deployer\WordPress\PluginUpgrader;

class InstallPlugin {

	/**
	 * @var Deployer
	 */
	private $deployer;

	/**
	 * @var PluginRepository
	 */
	private $plugins;

	/**
	 * @var PluginUpgrader
	 */
	private $upgrader;

	/**
	 * @var RepositoryFactory
	 */
	private $repositoryFactory;

	/**
	 * @param Deployer          $deployer
	 * @param PluginRepository  $plugins
	 * @param PluginUpgrader    $upgrader
	 * @param RepositoryFactory $repositoryFactory
	 */
	public function __construct( Deployer $deployer, PluginRepository $plugins, PluginUpgrader $upgrader, RepositoryFactory $repositoryFactory ) {
		$this->deployer          = $deployer;
		$this->plugins           = $plugins;
		$this->upgrader          = $upgrader;
		$this->repositoryFactory = $repositoryFactory;
	}

	public function handle( InstallPluginCommand $command ) {
		$plugin = new Plugin();

		$repository = $this->repositoryFactory->build(
			$command->type,
			$command->repository
		);

		if ( $command->private and $this->deployer->hasValidLicenseKey() ) {
			$repository->makePrivate();
		}

		$repository->setBranch( $command->branch );
		$plugin->setRepository( $repository );
		$plugin->setSubdirectory( $command->subdirectory );

		$command->dryRun ?: $this->upgrader->installPlugin( $plugin );

		if ( $command->subdirectory ) {
			$slug = end( explode( '/', $command->subdirectory ) );
		} else {
			$slug = $repository->getSlug();
		}

		$plugin = $this->plugins->fromSlug( $slug );
		$plugin->setRepository( $repository );
		$plugin->setPushToDeploy( $command->ptd );
		$plugin->setSubdirectory( $command->subdirectory );

		$this->plugins->store( $plugin );

		do_action( 'wpdeployer_plugin_was_installed', new PluginWasInstalled( $plugin ) );
	}
}
