<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<h2>WP Deployer Settings</h2>

<br>

<div class="nav-tab-wrapper">
	<a href="?page=wpdeployer" title="General" class="nav-tab<?php echo is_null($tab) ? ' nav-tab-active' : null; ?>">General</a>
	<a href="?page=wpdeployer&tab=github" title="GitHub" class="nav-tab<?php echo $tab === 'github' ? ' nav-tab-active' : null; ?>">GitHub</a>
	<a href="?page=wpdeployer&tab=bitbucket" title="Bitbucket" class="nav-tab<?php echo $tab === 'bitbucket' ? ' nav-tab-active' : null; ?>">Bitbucket</a>
	<a href="?page=wpdeployer&tab=gitlab" title="GitLab" class="nav-tab<?php echo $tab === 'gitlab' ? ' nav-tab-active' : null; ?>">GitLab</a>
	<a href="?page=wpdeployer&tab=log" title="Log" class="nav-tab<?php echo $tab === 'log' ? ' nav-tab-active' : null; ?>">Log</a>
</div>

<?php require $tabView; ?>
