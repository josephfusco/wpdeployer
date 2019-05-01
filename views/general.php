<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<?php settings_errors(); ?>

<br>

<form method="post" action="<?php echo esc_url( admin_url() ); ?>options.php">
	<?php settings_fields( 'deployer-license-settings' ); ?>
	<?php do_settings_sections( 'deployer-license-settings' ); ?>

	<p>Pain-free deployment of WordPress themes and plugins directly from GitHub, Bitbucket, and GitLab.</p>
	<p>Based off of the free version of <a href="https://wppusher.com/" target="_blank" rel="nofollow">WP Pusher</a></p>

	<br>

</form>
<br>
