<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?><h2>Edit <?php echo $plugin->name; ?></h2>
<hr>
<h3>
    <i class="fa <?php echo getHostIcon($plugin->host); ?>"></i>&nbsp;
    <a href="<?php echo getHostBaseUrl($plugin->host) . $plugin->repository; ?>" target="_blank">
        <?php echo $plugin->repository; ?>
    </a>
</h3>

<br>

<form action="" method="POST">
    <?php wp_nonce_field('edit-plugin'); ?>
    <input type="hidden" name="wpdeployer[action]" value="edit-plugin">
    <input type="hidden" name="wpdeployer[file]" value="<?php echo $plugin->file; ?>">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row">
                <label>Plugin repository</label>
            </th>
            <td>
                <input class="regular-text" type="text" name="wpdeployer[repository]" value="<?php echo $plugin->repository; ?>">
                <p class="description">Example: wpdeployer/awesome-wordpress-theme</p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label>Repository branch</label>
            </th>
            <td>
                <input placeholder="master" type="text" name="wpdeployer[branch]" value="<?php echo $plugin->repository->getBranch(); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label>Repository subdirectory</label>
            </th>
            <td>
                <input name="wpdeployer[subdirectory]" type="text" class="regular-text" placeholder="Optional" value="<?php echo $plugin->getSubdirectory(); ?>">
                <p class="description">Only relevant if your plugin resides in a subdirectory of the repository.</p>
                <p class="description">Example: <strong>awesome-plugin</strong> or <strong>plugins/awesome-plugin</strong></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label></label>
            </th>
            <td>
                <label><input type="checkbox" name="wpdeployer[ptd]" <?php echo ($plugin->pushToDeploy) ? 'checked' : null; ?>> Push-to-Deploy</label>
            </td>
        </tr>
        </tbody>
    </table>
    <br>
    <input value="Save changes" type="submit" class="button button-primary">
</form>
<br><br>
<form action="" method="POST">
    <?php wp_nonce_field('unlink-plugin'); ?>
    <input type="hidden" name="wpdeployer[action]" value="unlink-plugin">
    <input type="hidden" name="wpdeployer[file]" value="<?php echo $plugin->file; ?>">
    <input type="submit" class="button button-delete" value="Unlink plugin" style="float:right;">
</form>
<a href="?page=wpdeployer-plugins">Back to plugins</a>
<br><br>
