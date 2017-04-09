<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?><h2>WP Deployer Themes <a href="<?php echo admin_url( 'admin.php?page=wpdeployer-themes-create' ); ?>" class="page-title-action">Add New</a></h2>

<br>

<div class="theme-browser rendered">
    <div class="themes">
        <?php foreach ($themes as $theme) { ?>
            <div class="theme wpdeployer-package">
                <h3 class="theme-name repo-name"><i class="fa <?php echo getHostIcon($theme->host); ?>"></i>&nbsp; <?php echo $theme->repository; ?></h3>
                <div class="theme-screenshot package-info">
                    <div class="content">
                        <h3><?php echo $theme->name; ?></h3>
                        <p>Branch: <code><?php echo $theme->repository->getBranch(); ?></code></p>
                        <p>Push-to-Deploy: <code><?php echo ($theme->pushToDeploy) ? 'enabled' : 'disabled'; ?></code></p>
                        <p>Push-to-Deploy URL:<br><input class="push-to-deploy" type="text" value="<?php echo $theme->getPushToDeployUrl(); ?>" disabled></p>
                        <?php if ($theme->hasSubdirectory()) { ?>
                            <p>Subdirectory: <code><?php echo $theme->getSubdirectory(); ?></code></p>
                        <?php } ?>
                        <form action="" method="POST">
                            <?php wp_nonce_field('update-theme'); ?>
                            <input type="hidden" name="wpdeployer[action]" value="update-theme">
                            <input type="hidden" name="wpdeployer[repository]" value="<?php echo $theme->repository; ?>">
                            <input type="hidden" name="wpdeployer[stylesheet]" value="<?php echo $theme->stylesheet; ?>">
                            <button type="submit" class="button button-primary button-update-package">Update</button>
                        </form>
                        <a href="?page=wpdeployer-themes&repo=<?php echo urlencode($theme->repository); ?>" type="submit" class="button button-secondary button-save-package">Edit</a>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="theme add-new-theme">
            <a href="?page=wpdeployer-themes-create">
                <div class="theme-screenshot"><span></span></div>
                <h3 class="theme-name">Install New Theme</h3>
            </a>
        </div>
    </div>
    <br class="clear">
</div>
