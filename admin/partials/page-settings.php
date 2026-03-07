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

	<hr />
	<h2><?php esc_html_e( 'Platform Management', 'coachpro-ai-teacher-social-branding' ); ?></h2>
	<p><?php esc_html_e( 'Manage platform visibility, order, naming, icon and branding colors.', 'coachpro-ai-teacher-social-branding' ); ?></p>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="cpai_tsb_save_data" />
		<input type="hidden" name="cpai_tsb_action_type" value="save_platform_directory" />
		<?php wp_nonce_field( 'cpai_tsb_save_data', 'cpai_tsb_nonce' ); ?>

		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Platform', 'coachpro-ai-teacher-social-branding' ); ?></th>
					<th><?php esc_html_e( 'Slug', 'coachpro-ai-teacher-social-branding' ); ?></th>
					<th><?php esc_html_e( 'Urdu Name', 'coachpro-ai-teacher-social-branding' ); ?></th>
					<th><?php esc_html_e( 'Icon Class', 'coachpro-ai-teacher-social-branding' ); ?></th>
					<th><?php esc_html_e( 'Brand Color', 'coachpro-ai-teacher-social-branding' ); ?></th>
					<th><?php esc_html_e( 'Light Background', 'coachpro-ai-teacher-social-branding' ); ?></th>
					<th><?php esc_html_e( 'Sort', 'coachpro-ai-teacher-social-branding' ); ?></th>
					<th><?php esc_html_e( 'Active', 'coachpro-ai-teacher-social-branding' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $platforms as $slug => $platform ) : ?>
					<tr>
						<td><input type="text" name="platforms[<?php echo esc_attr( $slug ); ?>][name_en]" value="<?php echo esc_attr( $platform['name_en'] ); ?>" /></td>
						<td><code><?php echo esc_html( $slug ); ?></code></td>
						<td><input type="text" dir="rtl" name="platforms[<?php echo esc_attr( $slug ); ?>][name_ur]" value="<?php echo esc_attr( $platform['name_ur'] ); ?>" /></td>
						<td><input type="text" name="platforms[<?php echo esc_attr( $slug ); ?>][icon]" value="<?php echo esc_attr( $platform['icon'] ); ?>" placeholder="fab fa-facebook-f" /></td>
						<td><input type="color" name="platforms[<?php echo esc_attr( $slug ); ?>][color]" value="<?php echo esc_attr( $platform['color'] ); ?>" /></td>
						<td><input type="color" name="platforms[<?php echo esc_attr( $slug ); ?>][light_color]" value="<?php echo esc_attr( $platform['light_color'] ); ?>" /></td>
						<td><input type="number" min="0" style="width:75px;" name="platforms[<?php echo esc_attr( $slug ); ?>][sort_order]" value="<?php echo esc_attr( $platform['sort_order'] ); ?>" /></td>
						<td><label><input type="checkbox" name="platforms[<?php echo esc_attr( $slug ); ?>][enabled]" value="1" <?php checked( ! empty( $platform['enabled'] ) ); ?> /></label></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Save Platform Management', 'coachpro-ai-teacher-social-branding' ); ?></button></p>
	</form>

	<hr id="cpai-add-platform" />
	<h2><?php esc_html_e( 'Add New Platform', 'coachpro-ai-teacher-social-branding' ); ?></h2>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="cpai_tsb_save_data" />
		<input type="hidden" name="cpai_tsb_action_type" value="add_platform" />
		<?php wp_nonce_field( 'cpai_tsb_save_data', 'cpai_tsb_nonce' ); ?>

		<table class="form-table" role="presentation">
			<tr><th><?php esc_html_e( 'Platform Name', 'coachpro-ai-teacher-social-branding' ); ?></th><td><input class="regular-text" type="text" name="new_platform[name_en]" required /></td></tr>
			<tr><th><?php esc_html_e( 'Slug', 'coachpro-ai-teacher-social-branding' ); ?></th><td><input class="regular-text" type="text" name="new_platform[slug]" placeholder="linkedin" required /></td></tr>
			<tr><th><?php esc_html_e( 'Urdu Name', 'coachpro-ai-teacher-social-branding' ); ?></th><td><input class="regular-text" dir="rtl" type="text" name="new_platform[name_ur]" /></td></tr>
			<tr><th><?php esc_html_e( 'Icon / Icon Class', 'coachpro-ai-teacher-social-branding' ); ?></th><td><input class="regular-text" type="text" name="new_platform[icon]" placeholder="fab fa-linkedin-in" /></td></tr>
			<tr><th><?php esc_html_e( 'Brand Color', 'coachpro-ai-teacher-social-branding' ); ?></th><td><input type="color" name="new_platform[color]" value="#2563eb" /></td></tr>
			<tr><th><?php esc_html_e( 'Light Background Color', 'coachpro-ai-teacher-social-branding' ); ?></th><td><input type="color" name="new_platform[light_color]" value="#eff6ff" /></td></tr>
			<tr><th><?php esc_html_e( 'Sort Order', 'coachpro-ai-teacher-social-branding' ); ?></th><td><input type="number" min="0" name="new_platform[sort_order]" value="<?php echo esc_attr( ( count( $platforms ) + 1 ) * 10 ); ?>" /></td></tr>
			<tr><th><?php esc_html_e( 'Active', 'coachpro-ai-teacher-social-branding' ); ?></th><td><label><input type="checkbox" name="new_platform[enabled]" value="1" checked="checked" /> <?php esc_html_e( 'Enable immediately', 'coachpro-ai-teacher-social-branding' ); ?></label></td></tr>
		</table>

		<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Add Platform', 'coachpro-ai-teacher-social-branding' ); ?></button></p>
	</form>
</div>
