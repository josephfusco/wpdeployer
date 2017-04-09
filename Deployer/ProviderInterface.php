<?php

namespace Deployer;

interface ProviderInterface
{
    public function register(Deployer $deployer);
}
