<?php

namespace Deployer\Handlers;

use Deployer\Actions\PluginWasEdited;
use Deployer\Commands\EditPlugin as EditPluginCommand;
use Deployer\Git\Repository;
use Deployer\Storage\PluginRepository;

class EditPlugin
{
    /**
     * @var PluginRepository
     */
    private $plugins;

    /**
     * @param PluginRepository $plugins
     * @internal param Dashboard $dashboard
     */
    public function __construct(PluginRepository $plugins)
    {
        $this->plugins = $plugins;
    }

    public function handle(EditPluginCommand $command)
    {
        $repository = new Repository($command->repository);
        $repository->setBranch($command->branch);

        $this->plugins->editPlugin($command->file, array(
            'repository' => $repository,
            'branch' => $repository->getBranch(),
            'status' => $command->status,
            'ptd' => $command->pushToDeploy,
            'subdirectory' => $command->subdirectory,
        ));

        do_action('wpdeployer_plugin_was_edited', new PluginWasEdited(
            $this->plugins->deployerPluginFromRepository($repository)
        ));
    }
}
