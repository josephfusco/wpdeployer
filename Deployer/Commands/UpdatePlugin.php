<?php

namespace Deployer\Commands;

class UpdatePlugin
{
    public $file;
    public $repository;

    public function __construct($input)
    {
        $this->file = $input['file'];
        $this->repository = $input['repository'];
    }
}
