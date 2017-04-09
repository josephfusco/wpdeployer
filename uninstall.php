<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // Exit if accessed directly
}

require 'wpdeployer.php';

$deployer->make( 'Deployer\Storage\Database' )->uninstall();

// Deactivate license
$client = $deployer->make( 'Deployer\License\LicenseApi' );

$key = get_option( 'wpdeployer_license_key', false );

if ( $key ) {
	$client->removeLicenseFomSite( $key );
}

// Clean up
delete_option( 'hide-wpdeployer-welcome' );
delete_option( 'wpdeployer_token' );
delete_option( 'wpdeployer_license_key' );
delete_option( 'gh_token' );
delete_option( 'bb_user' );
delete_option( 'bb_pass' );
delete_option( 'bb_token' );
delete_option( 'gl_base_url' );
delete_option( 'gl_private_token' );
delete_option( 'deployer_logging_enabled' );
