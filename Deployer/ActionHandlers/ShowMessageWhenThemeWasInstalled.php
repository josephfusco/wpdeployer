<?php

namespace Deployer\ActionHandlers;

use Deployer\Actions\ThemeWasInstalled;
use Deployer\Dashboard;

class ShowMessageWhenThemeWasInstalled
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

    public function handle(ThemeWasInstalled $action)
    {
        if (is_multisite()) {
            $activationLink = network_admin_url()
                . "themes.php?action=enable&theme="
                . urlencode($action->theme->stylesheet)
                . "&_wpnonce="
                . wp_create_nonce('enable-theme_' . $action->theme->stylesheet);
        } else {
            $activationLink = get_admin_url()
                . "themes.php?action=activate&stylesheet="
                . urlencode($action->theme->stylesheet)
                . "&_wpnonce="
                . wp_create_nonce('switch-theme_' . $action->theme->stylesheet);
        }

        $this->dashboard->addMessage("Theme was successfully installed. Go ahead and <a href=\"{$activationLink}\">activate</a> it.");
    }
}
