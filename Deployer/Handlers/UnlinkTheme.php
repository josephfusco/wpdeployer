<?php

namespace Deployer\Handlers;

use Deployer\Actions\ThemeWasUnlinked;
use Deployer\Commands\UnlinkTheme as UnlinkThemeCommand;
use Deployer\Storage\ThemeRepository;

class UnlinkTheme
{
    /**
     * @var ThemeRepository
     */
    private $themes;

    /**
     * @param ThemeRepository $themes
     */
    public function __construct(ThemeRepository $themes)
    {
        $this->themes = $themes;
    }

    public function handle(UnlinkThemeCommand $command)
    {
        $this->themes->unlink($command->stylesheet);

        do_action('wpdeployer_theme_was_unlinked', new ThemeWasUnlinked);
    }
}
