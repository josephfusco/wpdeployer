<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?><h2>WP Deployer Plugins <a href="<?php echo admin_url( 'admin.php?page=wpdeployer-plugins-create' ); ?>" class="page-title-action">Add New</a></h2>

<br>

<div class="theme-browser rendered">
	<div class="themes">
		<?php foreach ( $plugins as $plugin ) { ?>
		<div class="theme wpdeployer-package">
			<h3 class="theme-name repo-name"><i class="fa <?php echo getHostIcon( $plugin->host ); ?>"></i>&nbsp; <?php echo $plugin->repository; ?></h3>
			<div class="theme-screenshot package-info">
				<div class="content">
					<h3><?php echo $plugin->name; ?></h3>
					<p>Branch: <code><?php echo $plugin->repository->getBranch(); ?></code></p>
					<p>Push-to-Deploy: <code><?php echo ( $plugin->pushToDeploy ) ? 'enabled' : 'disabled'; ?></code></p>
					<p>Push-to-Deploy URL:<br><input class="push-to-deploy" type="text" value="<?php echo $plugin->getPushToDeployUrl(); ?>" disabled></p>
					<?php if ( $plugin->hasSubdirectory() ) { ?>
						<p>Subdirectory: <code><?php echo $plugin->getSubdirectory(); ?></code></p>
					<?php } ?>
					<form action="" method="POST">
						<?php wp_nonce_field( 'update-plugin' ); ?>
						<input type="hidden" name="wpdeployer[action]" value="update-plugin">
						<input type="hidden" name="wpdeployer[repository]" value="<?php echo $plugin->repository; ?>">
						<input type="hidden" name="wpdeployer[file]" value="<?php echo $plugin->file; ?>">
						<button type="submit" class="button button-primary button-update-package">Update</button>
					</form>
					<a href="?page=wpdeployer-plugins&repo=<?php echo urlencode( $plugin->repository ); ?>" type="submit" class="button button-secondary button-save-package">Edit</a>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="theme add-new-theme">
			<a href="?page=wpdeployer-plugins-create">
				<div class="theme-screenshot"><span></span></div>
				<h3 class="theme-name">Install New Plugin</h3>
			</a>
		</div>
	</div>
	<br class="clear">
</div>
