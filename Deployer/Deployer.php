<?php

namespace Deployer;

use ReflectionClass;

class Deployer implements Container
{
    protected $services = array();

    public function init()
    {
        add_action('init', array($this->make('Deployer\Dispatcher'), 'dispatchWebhookRequest'));
        add_action('admin_init', array($this, 'registerPluginActionLinks'));
        add_action('admin_init', array($this, 'registerSettings'));
        add_action('admin_init', array($this->make('Deployer\Dispatcher'), 'dispatchPostRequests'));

        if (is_multisite())
            add_action('network_admin_menu', array($this, 'adminMenu'));
        else
            add_action('admin_menu', array($this, 'adminMenu'));

        // Add styles and scripts
        add_action('admin_enqueue_scripts', array($this, 'loadScripts'));

        // Nag users without Bitbucket token
        add_action('init', array($this, 'nagBitbucketUsers'));
    }

    public function activate() {
        $this->make('Deployer\Storage\Database')->install();
        $this->make('Deployer\Services\TokenGenerator')->addTokenOption();

        if ( ! get_option('gl_base_url', false))
            update_option('gl_base_url', 'https://gitlab.com');
    }

    public function adminMenu() {
        add_menu_page($this->getName(), $this->getName(), 'manage_options', 'wpdeployer', array($this->make('Deployer\Dashboard'), 'getIndex'), 'dashicons-marker');
        add_submenu_page('wpdeployer', 'Install Plugin', 'Install Plugin', 'manage_options', 'wpdeployer-plugins-create', array($this->make('Deployer\Dashboard'), 'getPluginsCreate'));
        add_submenu_page('wpdeployer', 'WP Deployer Plugins', 'Plugins', 'manage_options', 'wpdeployer-plugins', array($this->make('Deployer\Dashboard'), 'getPlugins'));
        add_submenu_page('wpdeployer', 'Install theme', 'Install theme', 'manage_options', 'wpdeployer-themes-create', array($this->make('Deployer\Dashboard'), 'getThemesCreate'));
        add_submenu_page('wpdeployer', 'WP Deployer Themes', 'Themes', 'manage_options', 'wpdeployer-themes', array($this->make('Deployer\Dashboard'), 'getThemes'));
    }

    public function getName() {
        return 'WP Deployer';
    }

    public function hasValidLicenseKey() {
        return (bool) get_option('wpdeployer_license_key', false);
    }

    public function registerPluginActionLinks() {
        $repository = $this->make('Deployer\Storage\PluginRepository');
        $plugins = $repository->allDeployerPlugins();
        $url = is_multisite()
            ? network_admin_url('admin.php?page=wpdeployer-plugins')
            : get_admin_url(null, 'admin.php?page=wpdeployer-plugins');

        $prefix = is_multisite()
            ? 'network_admin_plugin_action_links_'
            : 'plugin_action_links_';

        $link = '<a href="'. $url .'">Manage with WP Deployer</a>';

        foreach ($plugins as $plugin) {
            add_filter($prefix  . $plugin->file, function ($links) use ($link)
            {
                $links[] = $link;
                return $links;
            });
        }
    }

    public function registerSettings() {
        register_setting('deployer-token-settings', 'wpdeployer_token');
        add_filter('pre_update_option_wpdeployer_token', array($this->make('Deployer\Services\TokenGenerator'), 'refreshTokenFilter'), 10, 2);

        register_setting('deployer-license-settings', 'wpdeployer_license_key');
        add_filter('pre_update_option_wpdeployer_license_key', array($this->make('Deployer\License\LicenseManager'), 'activateSiteLicense'), 10, 2);

        register_setting('deployer-gh-settings', 'gh_token', array($this, 'checkGhToken'));
        register_setting('deployer-bb-settings', 'bb_token', array($this, 'checkBbToken'));
        register_setting('deployer-bb-settings', 'bb_user');
        register_setting('deployer-bb-settings', 'bb_pass', array($this, 'checkBbPass'));
        register_setting('deployer-gl-settings', 'gl_base_url');
        register_setting('deployer-gl-settings', 'gl_private_token', array($this, 'checkGlToken'));
        register_setting('deployer-enable-logging', 'deployer_logging_enabled');

        add_filter('pre_update_option_bb_token', array($this, 'removeBitbucketCredentialsWhenTokenIsAdded'), 10, 2);
    }

    public function nagBitbucketUsers() {
        $token = get_option('bb_token');
        $user = get_option('bb_user');

        $hasBitbucketToken = is_string($token) and ! $token !== '';
        $hasBitbucketUsername = is_string($user) and ! $user !== '';

        if ( ! $hasBitbucketToken and $hasBitbucketUsername) {
            add_action('admin_notices', function() {
                $message = "<a href=\"admin.php?page=wpdeployer&tab=bitbucket\">Please obtain a Bitbucket token</a> for easier and safer authentication with WP Deployer.";
                echo"<div class=\"update-nag\"><p>{$message}</p></div>";
            });
        }
    }

    public function removeBitbucketCredentialsWhenTokenIsAdded( $token ) {
        delete_option('bb_user');
        delete_option('bb_pass');

        return $token;
    }

    public function register(ProviderInterface $provider) {
        $provider->register($this);
    }

    public function loadScripts( $hook ) {
        wp_register_style('wpdeployer-styles', trailingslashit($this->deployerUrl) . 'assets/wpdeployer.css');
        wp_enqueue_style('wpdeployer-styles');

        wp_register_style('font_awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        wp_enqueue_style('font_awesome');

        wp_register_script('wpdeployer-js', trailingslashit($this->deployerUrl) . 'assets/wpdeployer.js');
        wp_enqueue_script('wpdeployer-js');
    }

    public function __get( $service ) {
        if ( ! isset( $this->services[ $service ] ) )
            return null;

        if ( ! is_callable( $this->services[ $service ] ) )
            return $this->services[$service];

        return $this->services[$service]($this);
    }

    public function __set( $service, $callback ) {
        $this->services[$service] = $callback;
    }

    public function checkGhToken( $token ) {
        return $this->checkSetting('gh_token', $token);
    }

    public function checkBbToken( $token ) {
        return $this->checkSetting('bb_token', $token);
    }

    public function checkBbPass( $password )
    {
        return $this->checkSetting('bb_pass', $password);
    }

    public function checkGlToken( $token ) {
        return $this->checkSetting('gl_private_token', $token);
    }

    protected function checkSetting( $name, $setting ) {
        $oldSetting = (get_option($name, '') != '')
            ? get_option($name)
            : false;

        if ( $setting == '' && $oldSetting !== false ) {
            return $oldSetting;
        }

        return $setting;
    }

    /**
     * Bind a service to the container.
     *
     * @param $alias
     * @param $concrete
     * @return mixed
     */
    public function bind( $alias, $concrete ) {
        $this->services[$alias] = $concrete;
    }

    /**
     * Request a service from the container.
     *
     * @param $alias
     * @return mixed
     */
    public function make( $alias ) {
        if (isset($this->services[$alias]) and is_callable($this->services[$alias])) {
            return call_user_func_array($this->services[$alias], array($this));
        }

        if (isset($this->services[$alias]) and is_object($this->services[$alias])) {
            return $this->services[$alias];
        }

        if (isset($this->services[$alias]) and class_exists($this->services[$alias])) {
            return $this->resolve($this->services[$alias]);
        }

        return $this->resolve($alias);
    }

    /**
     * Bind a singleton instance to the container.
     *
     * @param $alias
     * @param $binding
     */
    public function singleton( $alias, $binding ) {
        $this->bind($alias, $this->make($binding));
    }

    /**
     * Bind an action handler to an action.
     *
     * @param $tag
     * @param $handler
     * @param int $priority
     * @param int $acceptedArgs
     */
    public function addAction($tag, $handler, $priority = 10, $acceptedArgs = 1)
    {
        $deployer = $this;
        add_action($tag, function($action) use ($handler, $deployer) {
            $deployer->make($handler)->handle($action);
        }, $priority, $acceptedArgs);
    }

    private function resolve($class)
    {
        $reflection = new ReflectionClass($class);

        $constructor = $reflection->getConstructor();

        // Constructor is null
        if ( ! $constructor) {
            return new $class;
        }

        // Constructor with no parameters
        $params = $constructor->getParameters();

        if (count($params) === 0) {
            return new $class;
        }

        $newInstanceParams = array();

        foreach ($params as $param) {
            // @todo Here we should probably perform a bunch of checks, such as:
            // isArray(), isCallable(), isDefaultValueAvailable()
            // isOptional() etc.

            if (is_null($param->getClass())) {
                $newInstanceParams[] = null;
                continue;
            }

            $newInstanceParams[] = $this->make(
                $param->getClass()->getName()
            );
        }

        return $reflection->newInstanceArgs(
            $newInstanceParams
        );
    }
}
