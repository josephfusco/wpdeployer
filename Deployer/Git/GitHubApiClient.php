<?php

namespace Deployer\Git;

class GitHubApiClient {

	public function setUpWebhookForRepository( $webhook, GitHubRepository $repository ) {
		$token = get_option( 'gh_token' );

		$payload = json_encode(
			[
				'name'   => 'web',
				'active' => true,
				'config' => [
					'url' => html_entity_decode( $webhook ),
				],
			]
		);

		$url = "https://api.github.com/repos/{$repository->__toString()}/hooks?access_token={$token}";

		$response = wp_remote_post(
			$url,
			[
				'body'    => $payload,
				'headers' => [
					'Content-Type' => 'application/json',
				],
			]
		);

		if ( $response instanceof \WP_Error ) {
			throw new \Exception( 'Webhook was not updated on GitHub. Make sure a valid GitHub token is stored.' );
		}
	}
}
