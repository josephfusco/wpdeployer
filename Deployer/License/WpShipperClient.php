<?php

namespace Deployer\License;

class WpShipperClient implements LicenseApi {

	private $baseUrl = 'https://api.wpshipper.com/';

	public function getLicenseKey( $key ) {
		if ( ! $key or $key === '' ) {
			return false;
		}

		$result = wp_remote_get( $this->baseUrl . 'license-keys/' . $key );

		if ( is_wp_error( $result ) ) {
			add_settings_error( 'invalid-license-server-message', '', 'We couldn\'t check your license. Are you connected to the Internet?' );
		}

		$code = wp_remote_retrieve_response_code( $result );

		if ( $code !== 200 ) {
			return false;
		}

		$body  = wp_remote_retrieve_body( $result );
		$array = json_decode( $body, true );

		if ( ! $array ) {
			return false;
		}

		return LicenseKey::fromShipperResponseArray( $array );
	}

	public function registerKeyForSite( $key ) {
		$isValidKey = $this->getLicenseKey( $key );

		if ( ! $isValidKey ) {
			return false;
		}

		// Try to register new license
		$args   = [
			'body' => json_encode(
				[
					'site' => get_site_url(),
				]
			),
		];
		$result = wp_remote_post( $this->baseUrl . 'license-keys/' . $key . '/licenses', $args );
		$code   = wp_remote_retrieve_response_code( $result );

		if ( $code === 200 ) {
			return $key;
		}

		// Error handling
		$body  = wp_remote_retrieve_body( $result );
		$array = json_decode( $body, true );

		if ( ! $array ) {
			return false;
		}

		if ( isset( $array['message'] ) ) {
			add_settings_error( 'invalid-license-server-message', '', $array['message'] );
		}
	}

	public function removeLicenseFomSite( $key ) {
		$args       = [
			'method' => 'DELETE',
		];
		$encodedUrl = urlencode( base64_encode( get_site_url() ) );
		$result     = wp_remote_post( $this->baseUrl . 'license-keys/' . $key . '/licenses/' . $encodedUrl, $args );
		$code       = wp_remote_retrieve_response_code( $result );

		if ( $code === 200 ) {
			return false;
		}

		add_settings_error( 'invalid-license-server-message', '', 'License could not be deleted from site. Please contact support.' );

		return $key;
	}
}
