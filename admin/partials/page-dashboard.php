<div class="wrap">
	<h1><?php esc_html_e( 'Social Branding Dashboard', 'coachpro-ai-teacher-social-branding' ); ?></h1>
	<p><?php esc_html_e( 'Use platform-specific pages to manage questions and visual controls independently.', 'coachpro-ai-teacher-social-branding' ); ?></p>

	<table class="widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Platform', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<th><?php esc_html_e( 'Status', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<th><?php esc_html_e( 'Questions', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'coachpro-ai-teacher-social-branding' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $platforms as $slug => $platform ) : ?>
				<tr>
					<td><strong><?php echo esc_html( $platform['name_en'] ); ?></strong></td>
					<td>
						<?php echo ! empty( $platform['enabled'] ) ? esc_html__( 'Enabled', 'coachpro-ai-teacher-social-branding' ) : esc_html__( 'Disabled', 'coachpro-ai-teacher-social-branding' ); ?>
					</td>
					<td><?php echo esc_html( count( $platform['questions'] ) ); ?></td>
					<td>
						<a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-' . $slug ) ); ?>">
							<?php esc_html_e( 'Manage', 'coachpro-ai-teacher-social-branding' ); ?>
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
