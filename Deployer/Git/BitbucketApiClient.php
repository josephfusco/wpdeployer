<?php

namespace Deployer\Git;

class BitbucketApiClient
{
    public function setUpWebhookForRepository($webhook, BitbucketRepository $repository) {
        $token = get_option('bb_token');
        $accessToken = $repository->getAccessTokenFromRefreshToken($token);

        // Check if webhook already exists
        $url = "https://api.bitbucket.org/2.0/repositories/{$repository->__toString()}/hooks?access_token={$accessToken}";

        $hookName = 'WP Deployer: ' . get_site_url();

        $response = wp_remote_get($url);

        if ($response instanceof \WP_Error) {
            throw new \Exception('Webhook was not updated on Bitbucket. Make sure a valid Bitbucket token is stored.');
        }

        $payload = json_decode(wp_remote_retrieve_body($response), true);

        // Check if hook is already set up
        foreach ($payload['values'] as $webhookPayload) {
            if ($webhookPayload['description'] === $hookName) {
                // Hook already exists
                return null;
            }
        }

        // Proceed to set up a new webhook
        $payload = json_encode(array(
            'description' => $hookName,
            'url' => html_entity_decode($webhook),
            'active' => true,
            'events' => array(
                'repo:push',
            ),
        ));

        $url = "https://api.bitbucket.org/2.0/repositories/{$repository->__toString()}/hooks?access_token={$accessToken}";

        $response = wp_remote_post($url, array(
            'body' => $payload,
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
        ));

        $responseCode = wp_remote_retrieve_response_code($response);

        if ($responseCode === 400) {
            throw new \Exception('Webhook was not updated on Bitbucket. Make sure a valid Bitbucket token is stored. (400 bad request)');
        }

        if ($response instanceof \WP_Error) {
            throw new \Exception('Webhook was not updated on Bitbucket. Make sure a valid Bitbucket token is stored.');
        }
    }
}
