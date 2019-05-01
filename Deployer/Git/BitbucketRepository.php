<?php

namespace Deployer\Git;

use Exception;

class BitbucketRepository extends Repository {

	public $code = 'bb';

	public function getZipUrl() {
		$url = 'https://bitbucket.org/' . $this->handle . '/get/' . $this->getBranch() . '.zip?dir=/wpdeployer';

		if ( ! $this->isPrivate() ) {
			return $url;
		}

		$token = get_option( 'bb_token' );

		// If token is present, use that to get the actual token ...
		if ( is_string( $token ) and $token !== '' ) {
			$accessToken = $this->getAccessTokenFromRefreshToken( $token );

			return $url . '&access_token=' . $accessToken;
		}

		// ... Otherwise, use basic auth.
		add_filter( 'http_request_args', [ $this, 'bitbucketBasicAuth' ], 10, 2 );

		return $url;
	}

	public function bitbucketBasicAuth( $args, $url ) {
		if ( ! strstr( $url, 'https://bitbucket.org/' ) ) {
			return $args;
		}

		$user = get_option( 'bb_user' );
		$pass = get_option( 'bb_pass' );

		if ( is_string( $user ) && $user === '' ) {
			throw new Exception( 'No Bitbucket username stored.' );
		}

		if ( is_string( $pass ) && $pass === '' ) {
			throw new Exception( 'No Bitbucket password stored.' );
		}

		$args['headers']['Authorization'] = 'Basic ' . base64_encode( "{$user}:{$pass}" );

		return $args;
	}

	public function getAccessTokenFromRefreshToken( $token ) {
		$response = wp_remote_get( "https://cloud.wppusher.com/auth/bitbucket/refresh-token?refresh_token={$token}" );

		if ( is_wp_error( $response ) or empty( $response ) ) {
			// Something went wrong
			return '';
		}

		$json = json_decode( $response['body'], true );

		if ( ! isset( $json['access_token'] ) ) {
			// Something went wrong
			return '';
		}

		return $json['access_token'];
	}
}
