<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<?php settings_errors(); ?>

<form method="post" action="<?php echo esc_url( admin_url() ); ?>options.php">
	<?php settings_fields( 'deployer-bb-settings' ); ?>
	<?php do_settings_sections( 'deployer-bb-settings' ); ?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label>Bitbucket token</label>
				</th>
				<td>
					<input name="bb_token" type="text" id="bb_token"  placeholder="<?php echo ( get_option( 'bb_token' ) ) ? '********' : null; ?>" class="regular-text">
					&nbsp;
					<a href="#" onclick="window.open('https://cloud.wppusher.com/auth/bitbucket', 'WP Deployer Authentication', 'height=800,width=1100'); return false;" class="button">
						Obtain a Bitbucket token
					</a>
					<p class="description">You only need a token if your repositories are private.</p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php submit_button( 'Save Bitbucket token' ); ?>
</form>
