<?php

namespace Deployer\Actions;

use Deployer\Plugin;

class PluginWasEdited
{
    /**
     * @var Plugin
     */
    public $plugin;

    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
    }
}
