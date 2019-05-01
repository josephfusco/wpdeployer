<?php

namespace Deployer\Handlers;

use Deployer\Actions\ThemeWasEdited;
use Deployer\Commands\EditTheme as EditThemeCommand;
use Deployer\Git\Repository;
use Deployer\Storage\ThemeRepository;

class EditTheme
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

    public function handle(EditThemeCommand $command)
    {
        $repository = new Repository($command->repository);
        $repository->setBranch($command->branch);

        $this->themes->editTheme($command->stylesheet, array(
            'repository' => $repository,
            'branch' => $repository->getBranch(),
            'status' => $command->status,
            'ptd' => $command->pushToDeploy,
            'subdirectory' => $command->subdirectory,
        ));

        do_action('wpdeployer_theme_was_edited', new ThemeWasEdited(
            $this->themes->deployerThemeFromRepository($repository)
        ));
    }
}
