<?php

namespace Deployer\Handlers;

use Deployer\Actions\ThemeWasUpdated;
use Deployer\Commands\UpdateTheme as UpdateThemeCommand;
use Deployer\Storage\ThemeRepository;
use Deployer\WordPress\ThemeUpgrader;

class UpdateTheme {

	/**
	 * @var ThemeRepository
	 */
	private $themes;

	/**
	 * @var ThemeUpgrader
	 */
	private $upgrader;

	/**
	 * @param ThemeRepository $themes
	 * @param ThemeUpgrader   $upgrader
	 */
	public function __construct( ThemeRepository $themes, ThemeUpgrader $upgrader ) {
		$this->themes   = $themes;
		$this->upgrader = $upgrader;
	}

	public function handle( UpdateThemeCommand $command ) {
		$theme = $this->themes->deployerThemeFromRepository( $command->repository );

		$this->upgrader->upgradeTheme( $theme );

		do_action( 'wpdeployer_theme_was_updated', new ThemeWasUpdated( $theme ) );
	}
}
