<?php

namespace Deployer\WordPress;

require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/misc.php';

use Plugin_Upgrader;
use Deployer\Log\Logger;
use Deployer\Plugin;

class PluginUpgrader extends Plugin_Upgrader {

	public $plugin;

	/**
	 * @param PluginUpgraderSkin $skin
	 */
	public function __construct( PluginUpgraderSkin $skin ) {
		parent::__construct( $skin );
	}

	public function installPlugin( Plugin $plugin ) {
		add_filter( 'upgrader_source_selection', [ $this, 'upgraderSourceSelectionFilter' ], 10, 3 );

		$this->plugin = $plugin;

		parent::install( $this->plugin->repository->getZipUrl() );

		// Make sure we get out of maintenance mode
		$this->maintenance_mode( false );
	}

	public function upgradePlugin( Plugin $plugin ) {
		$reActivatePlugin            = is_plugin_active( (string) $plugin );
		$reActivatePluginNetworkWide = is_plugin_active_for_network( (string) $plugin );

		add_filter( 'pre_site_transient_update_plugins', [ $this, 'preSiteTransientUpdatePluginsFilter' ], 10, 3 );
		add_filter( 'upgrader_source_selection', [ $this, 'upgraderSourceSelectionFilter' ], 10, 3 );

		$this->plugin = $plugin;
		parent::upgrade( $this->plugin->file );

		if ( $reActivatePlugin ) {
			if ( ! is_plugin_active( (string) $plugin ) ) {
				activate_plugin( $plugin, null, $network_wide = $reActivatePluginNetworkWide, $silent = true );
			}
		}

		// Make sure we get out of maintenance mode
		$this->maintenance_mode( false );
	}

	public function upgraderSourceSelectionFilter( $source, $remote_source, $upgrader ) {
		if ( $upgrader->plugin->hasSubdirectory() ) {
			$source = trailingslashit( $source ) . trailingslashit( $upgrader->plugin->getSubdirectory() );
		}

		$newSource = trailingslashit( $remote_source ) . trailingslashit( $upgrader->plugin->getSlug() );

		global $wp_filesystem;

		if ( ! $wp_filesystem->move( $source, $newSource, true ) ) {
			return new \WP_Error();
		}

		return $newSource;
	}

	public function preSiteTransientUpdatePluginsFilter( $transient ) {
		$options                                    = [ 'package' => $this->plugin->repository->getZipUrl() ];
		$transient->response[ $this->plugin->file ] = (object) $options;

		return $transient;
	}
}
