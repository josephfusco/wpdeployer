<?php

namespace Deployer\Git;

use Exception;

class GitHubRepository extends Repository
{
    public $code = 'gh';

    public function getZipUrl()
    {
        $url = 'https://api.github.com/repos/' . $this->handle . '/zipball/' . $this->getBranch() . '?dir=/wpdeployer';

        if ($this->isPrivate()) {
            $token = get_option('gh_token');

            if ( is_string($token) && $token === '')
                throw new Exception('No GitHub token stored.');

            return $url . "&access_token=" . $token;
        }

        return $url;
    }
}
