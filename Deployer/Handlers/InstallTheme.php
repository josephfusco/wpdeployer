<?php

namespace Deployer\Handlers;

use Deployer\Actions\ThemeWasInstalled;
use Deployer\Commands\InstallTheme as InstallThemeCommand;
use Deployer\Dashboard;
use Deployer\Git\RepositoryFactory;
use Deployer\Log\Logger;
use Deployer\Deployer;
use Deployer\Storage\ThemeRepository;
use Deployer\Theme;
use Deployer\WordPress\ThemeUpgrader;

class InstallTheme {

	/**
	 * @var Deployer
	 */
	private $deployer;

	/**
	 * @var RepositoryFactory
	 */
	private $repositoryFactory;

	/**
	 * @var ThemeRepository
	 */
	private $themes;

	/**
	 * @var ThemeUpgrader
	 */
	private $upgrader;

	/**
	 * @param Deployer          $deployer
	 * @param RepositoryFactory $repositoryFactory
	 * @param ThemeRepository   $themes
	 * @param ThemeUpgrader     $upgrader
	 */
	public function __construct( Deployer $deployer, RepositoryFactory $repositoryFactory, ThemeRepository $themes, ThemeUpgrader $upgrader ) {
		$this->deployer          = $deployer;
		$this->repositoryFactory = $repositoryFactory;
		$this->themes            = $themes;
		$this->upgrader          = $upgrader;
	}

	public function handle( InstallThemeCommand $command ) {
		$theme = new Theme();

		$repository = $this->repositoryFactory->build(
			$command->type,
			$command->repository
		);

		if ( $command->private and $this->deployer->hasValidLicenseKey() ) {
			$repository->makePrivate();
		}

		$repository->setBranch( $command->branch );

		$theme->setRepository( $repository );
		$theme->setSubdirectory( $command->subdirectory );

		$command->dryRun ?: $this->upgrader->installTheme( $theme );

		if ( $command->subdirectory ) {
			$slug = end( explode( '/', $command->subdirectory ) );
		} else {
			$slug = $repository->getSlug();
		}

		$theme = $this->themes->fromSlug( $slug );
		$theme->setRepository( $repository );
		$theme->setPushToDeploy( $command->ptd );
		$theme->setSubdirectory( $command->subdirectory );

		$this->themes->store( $theme );

		do_action( 'wpdeployer_theme_was_installed', new ThemeWasInstalled( $theme ) );
	}
}
