<?php

namespace Deployer\ActionHandlers;

use Deployer\Actions\ThemeWasUpdated;
use Deployer\Dashboard;

class ShowMessageWhenThemeWasUpdated
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

    /**
     * @param ThemeWasUpdated $action
     */
    public function handle(ThemeWasUpdated $action)
    {
        $this->dashboard->addMessage('Theme was successfully updated.');
    }
}
