<?php

namespace Deployer\ActionHandlers;

use Deployer\ProviderInterface;
use Deployer\Deployer;

class ActionHandlerProvider implements ProviderInterface
{
    public function register(Deployer $deployer)
    {
        // Plugin was installed
        $deployer->addAction('wpdeployer_plugin_was_installed', 'Deployer\ActionHandlers\LogWhenPluginWasInstalled');
        $deployer->addAction('wpdeployer_plugin_was_installed', 'Deployer\ActionHandlers\ShowMessageWhenPluginWasInstalled');
        $deployer->addAction('wpdeployer_plugin_was_installed', 'Deployer\ActionHandlers\SetUpWebhookForPlugin');

        // Plugin was edited
        $deployer->addAction('wpdeployer_plugin_was_edited', 'Deployer\ActionHandlers\ShowMessageWhenPluginWasEdited');
        $deployer->addAction('wpdeployer_plugin_was_edited', 'Deployer\ActionHandlers\SetUpWebhookForPlugin');

        // Plugin was updated
        $deployer->addAction('wpdeployer_plugin_was_updated', 'Deployer\ActionHandlers\LogWhenPluginWasUpdated');
        $deployer->addAction('wpdeployer_plugin_was_updated', 'Deployer\ActionHandlers\ShowMessageWhenPluginWasUpdated');

        // Plugin was unlinked
        $deployer->addAction('wpdeployer_plugin_was_unlinked', 'Deployer\ActionHandlers\ShowMessageWhenPluginWasUnlinked');

        // Theme was installed
        $deployer->addAction('wpdeployer_theme_was_installed', 'Deployer\ActionHandlers\LogWhenThemeWasInstalled');
        $deployer->addAction('wpdeployer_theme_was_installed', 'Deployer\ActionHandlers\ShowMessageWhenThemeWasInstalled');
        $deployer->addAction('wpdeployer_theme_was_installed', 'Deployer\ActionHandlers\SetUpWebhookForTheme');

        // Theme was edited
        $deployer->addAction('wpdeployer_theme_was_edited', 'Deployer\ActionHandlers\ShowMessageWhenThemeWasEdited');
        $deployer->addAction('wpdeployer_theme_was_edited', 'Deployer\ActionHandlers\SetUpWebhookForTheme');

        // Theme was update
        $deployer->addAction('wpdeployer_theme_was_updated', 'Deployer\ActionHandlers\LogWhenThemeWasUpdated');
        $deployer->addAction('wpdeployer_theme_was_updated', 'Deployer\ActionHandlers\ShowMessageWhenThemeWasUpdated');

        // Theme was unlinked
        $deployer->addAction('wpdeployer_theme_was_unlinked', 'Deployer\ActionHandlers\ShowMessageWhenThemeWasUnlinked');
    }
}
