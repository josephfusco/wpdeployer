<?php

namespace Deployer\Commands;

class UpdatePackageFromWebhook
{
    public $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }
}
