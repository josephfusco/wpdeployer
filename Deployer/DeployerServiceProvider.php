<?php

namespace Deployer;

use Deployer\Log\Logger;

class DeployerServiceProvider implements ProviderInterface
{
    public function register(Deployer $deployer)
    {
        // Bind the Deployer instance itself to the container
        $deployer->bind('Deployer\Deployer', $deployer);

        // Initialise logger from log file
        $deployer->bind('Deployer\Log\Logger', function(Deployer $deployer) {
            $log = Logger::file(trailingslashit($deployer->deployerPath) . 'deployerlog');
            return $log;
        });

        // Use EDD for licensing
        $deployer->bind('Deployer\License\LicenseApi', 'Deployer\License\EddLicenseApi');

        // Singletons must be last for now, since they call "make()"
        $deployer->singleton('Deployer\Dashboard', 'Deployer\Dashboard');
    }
}
