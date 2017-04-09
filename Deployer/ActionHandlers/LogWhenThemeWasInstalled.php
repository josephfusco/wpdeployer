<?php

namespace Deployer\ActionHandlers;

use Deployer\Actions\ThemeWasInstalled;
use Deployer\Log\Logger;

class LogWhenThemeWasInstalled
{
    /**
     * @var Logger
     */
    private $log;

    /**
     * @param Logger $log
     */
    public function __construct(Logger $log)
    {
        $this->log = $log;
    }

    public function handle(ThemeWasInstalled $action)
    {
        $this->log->info(
            "Theme '{name}' was successfully installed.",
            array('name' => $action->theme->name)
        );
    }
}
