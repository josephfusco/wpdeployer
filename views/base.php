<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?><div class="wrap wpdeployer-wrap">

<?php
foreach ( $messages as $message ) {
	if ( is_wp_error( $message ) ) { ?>

		<div class="error">
			<p>An error occured: <?php echo $message->get_error_message(); ?></p>
		</div>
	<?php } else { ?>
		<div class="updated"><p><?php echo $message; ?></p></div>
	<?php }
}
?>

	<?php include __DIR__ . '/' . $view . '.php'; ?>

</div>
