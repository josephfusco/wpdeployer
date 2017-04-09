<?php

namespace Deployer\Handlers;

use Deployer\Actions\PluginWasUnlinked;
use Deployer\Commands\UnlinkPlugin as UnlinkPluginCommand;
use Deployer\Storage\PluginRepository;

class UnlinkPlugin
{
    /**
     * @var PluginRepository
     */
    private $plugins;

    /**
     * @param PluginRepository $plugins
     */
    public function __construct(PluginRepository $plugins)
    {
        $this->plugins = $plugins;
    }

    public function handle(UnlinkPluginCommand $command)
    {
        $this->plugins->unlink($command->file);

        do_action('wpdeployer_plugin_was_unlinked', new PluginWasUnlinked);
    }
}
