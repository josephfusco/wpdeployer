<?php

namespace Deployer;

use Exception;
use InvalidArgumentException;
use Deployer\Commands\EditPlugin;
use Deployer\Commands\EditTheme;
use Deployer\Commands\InstallPlugin;
use Deployer\Commands\InstallTheme;
use Deployer\Commands\UnlinkPlugin;
use Deployer\Commands\UnlinkTheme;
use Deployer\Commands\UpdatePlugin;
use Deployer\Commands\UpdateTheme;
use Deployer\Commands\UpdatePackageFromWebhook;
use Deployer\License\LicenseManager;
use Deployer\Log\Logger;
use Deployer\Storage\Database;
use Deployer\Storage\PluginNotFound;
use Deployer\Storage\PluginRepository;
use Deployer\Storage\ThemeNotFound;
use Deployer\Storage\ThemeRepository;
use WP_Error;

class Dashboard
{
    public $messages = array();

    /**
     * @var Database
     */
    private $db;

    /**
     * @var LicenseManager
     */
    private $license;

    /**
     * @var Logger
     */
    private $log;

    /**
     * @var PluginRepository
     */
    private $plugins;

    /**
     * @var ThemeRepository
     */
    private $themes;

    /**
     * @param Database $db
     * @param LicenseManager $license
     * @param Logger $log
     * @param PluginRepository $plugins
     * @param Deployer $deployer
     * @param ThemeRepository $themes
     */
    public function __construct(Database $db, LicenseManager $license, Logger $log, PluginRepository $plugins, Deployer $deployer, ThemeRepository $themes)
    {
        $this->db = $db;
        $this->license = $license;
        $this->log = $log;
        $this->plugins = $plugins;
        $this->deployer = $deployer;
        $this->themes = $themes;
    }

    public function getIndex()
    {
        $data['log'] = $this->log;
        $data['license_key'] = $this->license->licenseKey();

        $data['tab'] = isset($_GET['tab']) ? $_GET['tab'] : null;

        switch ($data['tab']) {
            case 'github':
                $data['tabView'] = 'github.php';
                break;
            case 'bitbucket':
                $data['tabView'] = 'bitbucket.php';
                break;
            case 'gitlab':
                $data['tabView'] = 'gitlab.php';
                break;
            case 'log':
                $data['tabView'] = 'log.php';
                break;
            default:
                $data['tabView'] = 'general.php';
        }

        return $this->render('index', $data);
    }

    public function postClearLog($request)
    {
        $this->log->clear();
        $this->addMessage('Log was cleared!');
    }

    public function getPlugins()
    {
        if (isset($_GET['repo'])) {
            try {
                $plugin = $this->plugins->deployerPluginFromRepository($_GET['repo']);
                return $this->render('plugins/edit', compact('plugin'));
            } catch (PluginNotFound $e) {
                // Plugin doesn't exist, show index instead
            }
        }

        $data['plugins'] = $this->plugins->allDeployerPlugins();

        return $this->render('plugins/index', $data);
    }

    public function postEditPlugin($request)
    {
        $command = new EditPlugin($request);
        $this->execute($command);
    }

    public function postUpdatePlugin($request)
    {
        $command = new UpdatePlugin($request);
        $this->execute($command);
    }

    public function getPluginsCreate()
    {
        // Run cleanup of orphan packages
        $this->db->cleanup();

        return $this->render('plugins/create');
    }

    public function postInstallPlugin($request)
    {
        $command = new InstallPlugin($request);
        $this->execute($command);
    }

    public function getThemes()
    {
        if (isset($_GET['repo'])) {
            try {
                $theme = $this->themes->deployerThemeFromRepository($_GET['repo']);
                return $this->render('themes/edit', compact('theme'));
            } catch (ThemeNotFound $e)
            {
                // Theme not found, show index instead
            }
        }

        $data['themes'] = $this->themes->allDeployerThemes();

        return $this->render('themes/index', $data);
    }

    public function postEditTheme($request)
    {
        $command = new EditTheme($request);
        $this->execute($command);
    }

    public function postUpdateTheme($request)
    {
        $command = new UpdateTheme($request);
        $this->execute($command);
    }

    public function getThemesCreate()
    {
        // Run cleanup of orphan packages
        $this->db->cleanup();

        return $this->render('themes/create');
    }

    public function postInstallTheme($request)
    {
        $command = new InstallTheme($request);
        $this->execute($command);
    }

    public function postWebhook($repository)
    {
        $command = new UpdatePackageFromWebhook($repository);
        $this->execute($command);

        die();
    }

    public function postUnlinkPlugin($request)
    {
        $command = new UnlinkPlugin($request);
        $this->execute($command);
    }

    public function postUnlinkTheme($request)
    {
        $command = new UnlinkTheme($request);
        $this->execute($command);
    }

    public function addMessage($message)
    {
        $this->messages[] = $message;
    }

    public function execute($command)
    {
        $handlerClass = str_replace('Commands', 'Handlers', get_class($command));

        if ( ! class_exists($handlerClass)) {
            throw new InvalidArgumentException("Handler {$handlerClass} doesn't exist.");
        }

        $handler = $this->deployer->make($handlerClass);

        try {
            $handler->handle($command);
        } catch (Exception $e) {
            status_header(400);
            $this->messages[] = new WP_Error('wpdeployer_error', $e->getMessage());
            $this->log->error($e->getMessage());
        }
    }

    protected function render($view, $data = array())
    {
        if ( ! current_user_can('update_plugins') || ! current_user_can('update_themes') ) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $data['messages'] = $this->messages;
        $data['hasValidLicense'] = $this->deployer->hasValidLicenseKey();
        $data['name'] = $this->deployer->getName();

        // Extract data
        extract($data);

        return include __DIR__.'/../views/base.php';
    }
}
