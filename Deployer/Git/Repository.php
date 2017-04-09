<?php

namespace Deployer\Git;

use Exception;

class Repository
{
    protected $handle;
    protected $branch;
    protected $private = 0;

    public function __construct($handle)
    {
        if ( ! $this->validate($handle))
            throw new Exception("Repository is not valid.");
            
        $this->handle = $handle;
    }

    public function validate($repo)
    {
        // For now, don't validate repositories
        // preg_match('/^[a-zA-Z0-9_-\d\.]+[\/]+[a-zA-Z0-9_-\d\.]*+$/', $repo, $match);

        // if (count($match) === 0) return false;

        return true;
    }

    public function getBranch()
    {
        if ( ! $this->branch || $this->branch === '') return 'master';

        return $this->branch;
    }

    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    public function getSlug()
    {
        $elements = preg_split('/\//', $this->handle);

        if ( ! isset($elements[1]))
            throw new Exception('Repository could not be parsed.');

        return $elements[1];
    }

    public function makePrivate()
    {
        $this->private = 1;
    }

    public function isPrivate()
    {
        return $this->private;
    }

    public function __toString()
    {
        return $this->handle;
    }
}
