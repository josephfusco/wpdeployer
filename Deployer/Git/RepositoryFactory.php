<?php

namespace Deployer\Git;

use Exception;

class RepositoryFactory {

	protected $allowedTypes = [ 'gh', 'bb', 'gl' ];

	public function build( $type, $handle ) {
		if ( ! in_array( $type, $this->allowedTypes ) ) {
			throw new Exception( 'Repository type not allowed.' );
		}

		if ( $type === 'gh' ) {
			return new GitHubRepository( $handle );
		} elseif ( $type === 'bb' ) {
			return new BitbucketRepository( $handle );
		} elseif ( $type === 'gl' ) {
			return new GitLabRepository( $handle );
		}
	}
}
