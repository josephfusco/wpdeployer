<?php

namespace Deployer\ActionHandlers;

use Deployer\Actions\PluginWasUnlinked;
use Deployer\Dashboard;

class ShowMessageWhenPluginWasUnlinked
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
     * @param PluginWasUnlinked $action
     */
    public function handle(PluginWasUnlinked $action)
    {
        $this->dashboard->addMessage("Plugin was unlinked from WP Deployer. You can re-connect it with 'Dry run'.");
    }
}
