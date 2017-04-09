<?php

namespace Deployer\Actions;

use Deployer\Theme;

class ThemeWasInstalled
{
    /**
     * @var Theme
     */
    public $theme;

    /**
     * @param Theme $theme
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }
}
