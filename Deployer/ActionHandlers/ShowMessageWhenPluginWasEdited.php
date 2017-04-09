<?php

namespace Deployer\ActionHandlers;

use Deployer\Actions\PluginWasEdited;
use Deployer\Dashboard;

class ShowMessageWhenPluginWasEdited
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

    public function handle(PluginWasEdited $action)
    {
        $this->dashboard->addMessage('Plugin changes was successfully saved.');
    }
}
