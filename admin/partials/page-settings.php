<div class="wrap">
	<h1><?php esc_html_e( 'Global Settings', 'coachpro-ai-teacher-social-branding' ); ?></h1>
	<p><?php esc_html_e( 'Configure system-wide defaults that apply to all platform modules.', 'coachpro-ai-teacher-social-branding' ); ?></p>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="cpai_tsb_save_data" />
		<input type="hidden" name="cpai_tsb_action_type" value="save_settings" />
		<?php wp_nonce_field( 'cpai_tsb_save_data', 'cpai_tsb_nonce' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Dashboard Title', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<td><input type="text" class="regular-text" name="settings[dashboard_title]" value="<?php echo esc_attr( $settings['dashboard_title'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Default Language', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<td>
					<select name="settings[default_language]">
						<option value="en" <?php selected( $settings['default_language'], 'en' ); ?>><?php esc_html_e( 'English', 'coachpro-ai-teacher-social-branding' ); ?></option>
						<option value="ur" <?php selected( $settings['default_language'], 'ur' ); ?>><?php esc_html_e( 'Urdu', 'coachpro-ai-teacher-social-branding' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Primary Color', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<td><input type="color" name="settings[primary_color]" value="<?php echo esc_attr( $settings['primary_color'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Items Per Page', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<td><input type="number" min="1" name="settings[items_per_page]" value="<?php echo esc_attr( $settings['items_per_page'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Show Branding', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<td><label><input type="checkbox" name="settings[show_branding]" value="1" <?php checked( ! empty( $settings['show_branding'] ) ); ?> /> <?php esc_html_e( 'Display branding text in public module.', 'coachpro-ai-teacher-social-branding' ); ?></label></td>
			</tr>
		</table>

		<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Save Settings', 'coachpro-ai-teacher-social-branding' ); ?></button></p>
	</form>
</div>
