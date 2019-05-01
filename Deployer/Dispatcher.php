<?php

namespace Deployer;

use Deployer\Log\Logger;

class Dispatcher {

	/**
	 * @var Dashboard
	 */

	private $dashboard;
	/**
	 * @var Logger
	 */

	private $log;

	/**
	 * @param Dashboard $dashboard
	 * @param Logger    $log
	 */
	public function __construct( Dashboard $dashboard, Logger $log ) {
		$this->dashboard = $dashboard;
		$this->log       = $log;
	}

	public function dispatchPostRequests() {
		if ( isset( $_POST['wpdeployer'] ) ) {

			if ( ! current_user_can( 'update_plugins' ) || ! current_user_can( 'update_themes' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}

			$request = $_POST['wpdeployer'];

			switch ( $request['action'] ) {
				case 'clear-log':
					check_admin_referer( 'clear-log' );
					$this->dashboard->postClearLog( $request );
					break;

				case 'install-plugin':
					check_admin_referer( 'install-plugin' );
					$this->dashboard->postInstallPlugin( $request );
					break;

				case 'install-theme':
					check_admin_referer( 'install-theme' );
					$this->dashboard->postInstallTheme( $request );
					break;

				case 'edit-plugin':
					check_admin_referer( 'edit-plugin' );
					$this->dashboard->postEditPlugin( $request );
					break;

				case 'edit-theme':
					check_admin_referer( 'edit-theme' );
					$this->dashboard->postEditTheme( $request );
					break;

				case 'update-plugin':
					check_admin_referer( 'update-plugin' );
					$this->dashboard->postUpdatePlugin( $request );
					break;

				case 'update-theme':
					check_admin_referer( 'update-theme' );
					$this->dashboard->postUpdateTheme( $request );
					break;

				case 'unlink-plugin':
					check_admin_referer( 'unlink-plugin' );
					$this->dashboard->postUnlinkPlugin( $request );
					break;

				case 'unlink-theme':
					check_admin_referer( 'unlink-theme' );
					$this->dashboard->postUnlinkTheme( $request );
					break;

				default:
					break;
			}
		}
	}

	public function dispatchWebhookRequest() {
		if ( ! isset( $_GET['wpdeployer-hook'] ) ) {
			return;
		}

		if ( ! isset( $_GET['token'] ) || $_GET['token'] !== get_option( 'wpdeployer_token' ) ) {
			$this->log->error( 'Push-to-Deploy failed. Token was invalid.' );
			status_header( 400 );
			die();
		}

		if ( isset( $_GET['repo'] ) ) {
			$repository = base64_decode( $_GET['repo'] );
		} else {
			$this->log->error( 'Push-to-Deploy failed. No repository given.' );
			status_header( 400 );
			die();
		}

		$this->log->info( 'Push-to-Deploy was initiated.' );

		$this->dashboard->postWebhook( $repository );
	}
}
