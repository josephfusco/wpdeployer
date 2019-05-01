<?php

namespace Deployer\WordPress;

use Deployer\Actions\ThemeUpdateFailed;
use Theme_Upgrader_Skin;
use WP_Error;

class ThemeUpgraderSkin extends Theme_Upgrader_Skin
{
    protected $error;
    protected $feedback;

    public function after()
    {
        // WP doesn't sent all errors as actual error objects
        if ($this->error === 'up_to_date') {
            $this->error = new WP_Error('wpdeployer_error', 'Theme is up-to-date.');
        }

        // Optimise error messages
        if ($this->error and $this->error->get_error_code() === 'download_failed') {
            $this->error = new WP_Error('download_failed', $this->error->get_error_message() . ' Make sure repository handle is correct and that you have a valid token.<br>If you are using GitHub, try obtaining <a href="admin.php?page=wpdeployer&tab=github">a new token</a>.');
        }

        // Probably because Bitbucket token has been invalidated
        if ($this->error and $this->error->get_error_code() === 'incompatible_archive') {
            $this->error = new WP_Error('incompatible_archive', $this->error->get_error_message() . ' If you are using Bitbucket, maybe your token has been invalidated. Try obtaining <a href="admin.php?page=wpdeployer&tab=bitbucket">a new one</a>.');
        }

        if ( ! is_null($this->error)) {
            do_action('wpdeployer_theme_update_failed', new ThemeUpdateFailed(
                $this->error->get_error_message()
            ));

            throw new InstallFailed($this->error->get_error_message());
        }
    }

    public function before()
    {
        // ...
    }

    public function error($error)
    {
        $this->error = $error;
    }

    public function header()
    {
        // ...
    }

    public function feedback($string)
    {
        $this->feedback[$string] = true;
    }

    public function footer()
    {
        // ...
    }
}
