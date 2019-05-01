<?php

namespace Deployer\ActionHandlers;

use Deployer\Actions\ThemeWasUnlinked;
use Deployer\Dashboard;

class ShowMessageWhenThemeWasUnlinked
{
    /**
     * @var Dashboard
     */
    private $dashboard;

    /**
     * @param Dashboard $dashboard
     */
    public function __construct(Dashboard $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    public function handle(ThemeWasUnlinked $action)
    {
        $this->dashboard->addMessage("Theme was unlinked from WP Deployer. You can re-connect it with 'Dry run'.");
    }
}
