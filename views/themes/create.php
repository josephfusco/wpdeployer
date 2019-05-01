<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?><h2>Install New Theme</h2>

<form action="" method="POST">
	<?php wp_nonce_field( 'install-theme' ); ?>
	<input type="hidden" name="wpdeployer[action]" value="install-theme">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label>Theme repository</label>
				</th>
				<td>
					<input name="wpdeployer[repository]" type="text" class="regular-text" value="<?php echo ( isset( $_POST['wpdeployer']['repository'] ) ) ? $_POST['wpdeployer']['repository'] : ''; ?>">
					<p class="description">Example: wpdeployer/awesome-wordpress-theme</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label>Repository branch</label>
				</th>
				<td>
					<input name="wpdeployer[branch]" type="text" class="regular-text" placeholder="master" value="<?php echo ( isset( $_POST['wpdeployer']['branch'] ) ) ? $_POST['wpdeployer']['branch'] : ''; ?>">
					<p class="description">Defaults to <strong>master</strong> if left blank</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label>Repository subdirectory</label>
				</th>
				<td>
					<input name="wpdeployer[subdirectory]" type="text" class="regular-text" placeholder="Optional" value="<?php echo ( isset( $_POST['wpdeployer']['subdirectory'] ) ) ? $_POST['wpdeployer']['subdirectory'] : ''; ?>">
					<p class="description">Only relevant if your theme resides in a subdirectory of the repository.</p>
					<p class="description">Example: <strong>awesome-theme</strong> or <strong>plugins/awesome-theme</strong></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label>Repository host</label>
				</th>
				<td>
					<select name="wpdeployer[type]">
						<option value="gh"
						<?php
						if ( isset( $_POST['wpdeployer']['type'] ) && $_POST['wpdeployer']['type'] === 'gh' ) {
							echo 'selected="selected" ';}
						?>
						>GitHub</option>
						<option value="bb"
						<?php
						if ( isset( $_POST['wpdeployer']['type'] ) && $_POST['wpdeployer']['type'] === 'bb' ) {
							echo 'selected="selected" ';}
						?>
						>Bitbucket</option>
						<option value="gl"
						<?php
						if ( isset( $_POST['wpdeployer']['type'] ) && $_POST['wpdeployer']['type'] === 'gl' ) {
							echo 'selected="selected" ';}
						?>
						>GitLab</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"></th>
				<td>
					<label><input type="checkbox" name="wpdeployer[private]"
					<?php
					if ( isset( $_POST['wpdeployer']['private'] ) ) {
						echo 'checked';}
					?>
					> Repository is private</label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label></label>
				</th>
				<td>
					<label><input type="checkbox" name="wpdeployer[ptd]"
					<?php
					if ( isset( $_POST['wpdeployer']['ptd'] ) ) {
						echo 'checked';}
					?>
					> Push-to-Deploy</label>
					<p class="description">Automatically update on every push.</p>
				</td>
			</tr>
			<tr>
				<th scope="row"></th>
				<td>
					<label><input type="checkbox" name="wpdeployer[dry-run]"
					<?php
					if ( isset( $_POST['wpdeployer']['dry-run'] ) ) {
						echo 'checked';}
					?>
					> Dry run</label>
					<p class="description">For already installed themes</p>
					<p class="description">Folder name <strong>must</strong> have the same name as repository</p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php submit_button( 'Install theme' ); ?>
</form>
