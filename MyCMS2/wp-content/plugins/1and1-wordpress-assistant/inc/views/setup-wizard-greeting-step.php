<div class="wrap">
	<?php
	include_once( One_And_One_Wizard::get_views_dir_path().'setup-wizard-header.php' );
	One_And_One_Wizard_Header::render();
	?>

	<h3 class="clear"><?php esc_html_e( 'setup_assistant_greeting_title', '1and1-wordpress-wizard' ) ?></h3>
	<p><?php _e( 'setup_assistant_greeting_description', '1and1-wordpress-wizard' ); ?></p>

	<p>
		<a href="<?php echo admin_url( 'admin.php?page=1and1-wordpress-wizard&setup_action=choose_site' ); ?>" class="button button-primary">
			<?php esc_html_e( 'setup_assistant_greeting_ok', '1and1-wordpress-wizard' ) ?>
		</a>
		&nbsp; &nbsp;
		<a href="<?php echo admin_url( 'index.php?1and1-wordpress-wizard-cancel=1' ); ?>" class="button">
			<?php esc_html_e( 'setup_assistant_greeting_cancel', '1and1-wordpress-wizard' ) ?>
		</a>
	</p>
</div>