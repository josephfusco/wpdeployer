<?php
/**
 * Plugin Name: WP Deployer
 * Description: Pain-free deployment of WordPress themes and plugins directly from GitHub, Bitbucket, and GitLab.
 * Version: 1.1
 * Author: WP Deployer
 * License: GNU GENERAL PUBLIC LICENSE
 * GitHub Plugin URI: josephfusco/wpdeployer
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require __DIR__ . '/autoload.php';

use Deployer\ActionHandlers\ActionHandlerProvider;
use Deployer\Deployer;
use Deployer\DeployerServiceProvider;

$deployer = new Deployer;
$deployer->deployerPath = plugin_dir_path( __FILE__ );
$deployer->deployerUrl = plugin_dir_url( __FILE__ );
$deployer->register( new DeployerServiceProvider );
$deployer->register( new ActionHandlerProvider );

register_activation_hook( __FILE__, array( $deployer, 'activate' ) );

$deployer->init();

if ( ! function_exists( 'getHostIcon' ) ) {
    function getHostIcon( $host ) {
        if ( $host === 'gh' ) {
            return 'fa-github';
        } elseif ( $host === 'bb' ) {
            return 'fa-bitbucket';
        } else {
            return 'fa-gitlab';
        }
    }
}

if ( ! function_exists( 'getHostBaseUrl' ) ) {
    function getHostBaseUrl( $host ) {
        if ($host === 'gh') {
            return 'https://github.com/';
        } elseif ( $host === 'bb' ) {
            return 'https://bitbucket.org/';
        } elseif ( $host === 'gl' ) {
            return trailingslashit( get_option( 'gl_base_url' ) );
        } else {
            return null;
        }
    }
}

$hidePluginsFromUpdateChecks = function($args, $url) use ($deployer)
{
    if (0 !== strpos($url, 'https://api.wordpress.org/plugins/update-check')) {
        return $args;
    }

    $plugins = json_decode($args['body']['plugins'], true);

    $repository = $deployer->make('Deployer\Storage\PluginRepository');
    $pluginsToHide = array_keys($repository->allDeployerPlugins());
    $pluginsToHide[] = plugin_basename(__FILE__);

    foreach ($pluginsToHide as $plugin) {
        unset($plugins['plugins'][$plugin]);
        unset($plugins['active'][array_search($plugin, $plugins['active'])]);
    }

    $args['body']['plugins'] = json_encode($plugins);

    return $args;
};

$hideThemesFromUpdateChecks = function($args, $url) use ($deployer)
{
    if (0 !== strpos($url, 'https://api.wordpress.org/themes/update-check')) {
        return $args;
    }

    $themes = json_decode($args['body']['themes'], true);

    $repository = $deployer->make('Deployer\Storage\ThemeRepository');
    $themesToHide = array_keys($repository->allDeployerThemes());

    foreach ($themesToHide as $theme) {
        unset($themes['themes'][$theme]);

        if (isset($themes['active']) and in_array($themes['active'], $themesToHide)) {
            unset($themes['active']);
        }
    }

    $args['body']['themes'] = json_encode($themes);

    return $args;
};

add_filter('http_request_args', $hidePluginsFromUpdateChecks, 5, 2);
add_filter('http_request_args', $hideThemesFromUpdateChecks, 5, 2);

// Dismiss welcome hero
if (isset($_GET['wpdeployer-welcome']) and $_GET['wpdeployer-welcome'] == '0') {
    update_option('hide-wpdeployer-welcome', true);
}

if ( ! function_exists('deployerTableName()')) {
    function deployerTableName()
    {
        global $wpdb;
        $dbPrefix = is_multisite() ? $wpdb->base_prefix : $wpdb->prefix;

        return $dbPrefix . 'wpdeployer_packages';
    }
}

// Set license & disable welcome menu
update_option( 'wpdeployer_license_key', '1' );
update_option( 'hide-wpdeployer-welcome', '1' );
