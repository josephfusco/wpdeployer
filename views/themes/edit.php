<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?><h2>Edit <?php echo $theme->name; ?></h2>
<hr>
<h3>
	<i class="fa <?php echo getHostIcon( $theme->host ); ?>"></i>&nbsp;
	<a href="<?php echo getHostBaseUrl( $theme->host ) . $theme->repository; ?>" target="_blank">
		<?php echo $theme->repository; ?>
	</a>
</h3>

<br>

<form action="" method="POST">
	<?php wp_nonce_field( 'edit-theme' ); ?>
	<input type="hidden" name="wpdeployer[action]" value="edit-theme">
	<input type="hidden" name="wpdeployer[stylesheet]" value="<?php echo $theme->stylesheet; ?>">
	<table class="form-table">
		<tbody>
		<tr>
			<th scope="row">
				<label>Theme repository</label>
			</th>
			<td>
				<input class="regular-text" type="text" name="wpdeployer[repository]" value="<?php echo $theme->repository; ?>">
				<p class="description">Example: wpdeployer/awesome-wordpress-theme</p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label>Repository branch</label>
			</th>
			<td>
				<input placeholder="master" type="text" name="wpdeployer[branch]" value="<?php echo $theme->repository->getBranch(); ?>">
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label>Repository subdirectory</label>
			</th>
			<td>
				<input name="wpdeployer[subdirectory]" type="text" class="regular-text" placeholder="Optional" value="<?php echo $theme->getSubdirectory(); ?>">
				<p class="description">Only relevant if your theme resides in a subdirectory of the repository.</p>
				<p class="description">Example: <strong>awesome-theme</strong> or <strong>plugins/awesome-theme</strong></p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label></label>
			</th>
			<td>
				<label><input type="checkbox" name="wpdeployer[ptd]" <?php echo ( $theme->pushToDeploy ) ? 'checked' : null; ?>> Push-to-Deploy</label>
			</td>
		</tr>
		</tbody>
	</table>
	<br>
	<input value="Save changes" type="submit" class="button button-primary">
</form>
<br><br>
<form action="" method="POST">
	<?php wp_nonce_field( 'unlink-theme' ); ?>
	<input type="hidden" name="wpdeployer[action]" value="unlink-theme">
	<input type="hidden" name="wpdeployer[stylesheet]" value="<?php echo $theme->stylesheet; ?>">
	<input type="submit" class="button button-delete" value="Unlink theme" style="float:right;">
</form>
<a href="?page=wpdeployer-themes">Back to themes</a>
<br><br>
