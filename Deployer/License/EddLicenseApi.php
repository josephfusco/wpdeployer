<?php

namespace Deployer\License;

class EddLicenseApi implements LicenseApi {

	private $baseUrl = 'https://checkout.wppusher.com/';

	public function getLicenseKey( $key ) {
		if ( ! $key or $key === '' ) {
			return false;
		}

		$result = wp_remote_get( $this->baseUrl . '?edd_action=check_license&item_name=WP+Deployer&license=' . $key . '&url=' . home_url() );

		$code = wp_remote_retrieve_response_code( $result );

		if ( $code !== 200 ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $result );

		$array = json_decode( $body, true );

		if ( ! $array ) {
			return false;
		}

		if ( $array['license'] === 'invalid' ) {
			return false;
		}

		if ( $array['license'] !== 'active' and $array['license'] !== 'expired' ) {
			$key = $this->registerKeyForSite( $key );

			if ( ! $key ) {
				return false;
			}
		}

		// For backwards compatability:
		$array['token'] = $key;

		return LicenseKey::fromEddResponseArray( $array );
	}

	public function registerKeyForSite( $key ) {
		// Try to register new license
		$args   = [
			'edd_action' => 'activate_license',
			'item_name'  => 'WP+Deployer',
			'license'    => $key,
			'url'        => home_url(),
		];
		$result = wp_remote_get( add_query_arg( $args, $this->baseUrl ) );

		// Error handling
		$body  = wp_remote_retrieve_body( $result );
		$array = json_decode( $body, true );

		if ( ! $array ) {
			return false;
		}

		if ( isset( $array['error'] ) and $array['error'] === 'no_activations_left' ) {
			return false;
		}

		if ( $array['license'] !== 'valid' ) {
			return false;
		}

		return $key;
	}

	public function removeLicenseFomSite( $key ) {
		$args = [
			'edd_action' => 'deactivate_license',
			'item_name'  => 'WP+Deployer',
			'license'    => $key,
		];

		$result = wp_remote_get( add_query_arg( $args, $this->baseUrl ) );
		$status = wp_remote_retrieve_response_code( $result );

		if ( $status == 200 ) {
			return false;
		}

		return $key;
	}
}
