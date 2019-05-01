<?php

namespace Deployer\WordPress;

include_once(ABSPATH . 'wp-admin/includes/theme.php');
include_once(ABSPATH . 'wp-admin/includes/file.php');
include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
include_once(ABSPATH . 'wp-admin/includes/misc.php');

use Deployer\Dashboard;
use Deployer\Log\Logger;
use Theme_Upgrader;
use Deployer\Theme;

class ThemeUpgrader extends Theme_Upgrader
{
    public $theme;

    public function __construct(ThemeUpgraderSkin $skin)
    {
        parent::__construct($skin);
    }

    public function installTheme(Theme $theme)
    {
        add_filter('upgrader_source_selection', array($this, 'upgraderSourceSelectionFilter'), 10, 3);

        $this->theme = $theme;

        parent::install($this->theme->repository->getZipUrl());

        // Make sure we get out of maintenance mode
        $this->maintenance_mode(false);
    }

    public function upgradeTheme(Theme $theme)
    {
        add_filter("pre_site_transient_update_themes", array($this, 'preSiteTransientUpdateThemesFilter'), 10, 3);
        add_filter('upgrader_source_selection', array($this, 'upgraderSourceSelectionFilter'), 10, 3);

        $this->theme = $theme;

        parent::upgrade($this->theme->stylesheet);

        // Make sure we get out of maintenance mode
        $this->maintenance_mode(false);
    }

    public function upgraderSourceSelectionFilter($source, $remote_source, $upgrader)
    {
        if ($upgrader->theme->hasSubdirectory()) {
            $source = trailingslashit($source) . trailingslashit($upgrader->theme->getSubdirectory());
        }

        $newSource = trailingslashit($remote_source) . trailingslashit($upgrader->theme->getSlug());

        global $wp_filesystem;

        if ( ! $wp_filesystem->move($source, $newSource, true))
            return new \WP_Error();

        return $newSource;
    }

    public function preSiteTransientUpdateThemesFilter($transient)
    {
        $options = array('package' => $this->theme->repository->getZipUrl());
        $transient->response[$this->theme->stylesheet] = $options;

        return $transient;
    }
}
