<div class="wrap cpai-tsb-dashboard-wrap">
	<h1><?php esc_html_e( 'Social Branding Dashboard', 'coachpro-ai-teacher-social-branding' ); ?></h1>
	<p><?php esc_html_e( 'Manage your active platforms and reserve space for future channels.', 'coachpro-ai-teacher-social-branding' ); ?></p>

	<?php
	$dashboard_cards = array(
		array(
			'name'       => 'Facebook',
			'source_slug'=> 'facebook',
			'brand_class'=> 'is-facebook',
			'logo'       => 'f',
		),
		array(
			'name'       => 'X / Twitter',
			'source_slug'=> 'youtube',
			'brand_class'=> 'is-x',
			'logo'       => 'X',
		),
		array(
			'name'       => 'Instagram',
			'source_slug'=> 'instagram',
			'brand_class'=> 'is-instagram',
			'logo'       => '◎',
		),
		array(
			'name'       => 'LinkedIn',
			'source_slug'=> 'tiktok',
			'brand_class'=> 'is-linkedin',
			'logo'       => 'in',
		),
	);
	?>

	<div class="cpai-tsb-platform-grid" role="list" aria-label="<?php esc_attr_e( 'Platform cards', 'coachpro-ai-teacher-social-branding' ); ?>">
		<?php foreach ( $dashboard_cards as $card ) : ?>
			<?php
			$source_slug    = $card['source_slug'];
			$platform       = isset( $platforms[ $source_slug ] ) ? $platforms[ $source_slug ] : null;
			$question_count = $platform ? count( $platform['questions'] ) : 0;
			$manage_url     = admin_url( 'admin.php?page=' . $this->plugin_name . '-' . $source_slug );
			?>
			<article class="cpai-tsb-platform-card <?php echo esc_attr( $card['brand_class'] ); ?>" role="listitem">
				<div class="cpai-tsb-platform-head">
					<span class="cpai-tsb-platform-logo" aria-hidden="true"><?php echo esc_html( $card['logo'] ); ?></span>
					<div>
						<h2><?php echo esc_html( $card['name'] ); ?></h2>
						<p class="cpai-tsb-status">
							<span class="cpai-tsb-status-icon" aria-hidden="true">✓</span>
							<?php esc_html_e( 'Active', 'coachpro-ai-teacher-social-branding' ); ?>
						</p>
					</div>
				</div>

				<p class="cpai-tsb-questions">
					<span class="cpai-tsb-questions-count"><?php echo esc_html( $question_count ); ?></span>
					<?php esc_html_e( 'Questions', 'coachpro-ai-teacher-social-branding' ); ?>
				</p>

				<a class="button cpai-tsb-manage-button" href="<?php echo esc_url( $manage_url ); ?>">
					<?php esc_html_e( 'Manage', 'coachpro-ai-teacher-social-branding' ); ?>
				</a>
			</article>
		<?php endforeach; ?>

		<?php for ( $index = 0; $index < 4; $index++ ) : ?>
			<div class="cpai-tsb-placeholder-card" role="listitem">
				<span>+</span>
				<p><?php esc_html_e( 'Add Platform', 'coachpro-ai-teacher-social-branding' ); ?></p>
			</div>
		<?php endfor; ?>
	</div>
</div>
