<?php
$platform_count = count( $platforms );
$active_count   = 0;
$question_total = 0;

foreach ( $platforms as $platform ) {
	if ( ! empty( $platform['enabled'] ) ) {
		++$active_count;
	}
	$question_total += isset( $platform['questions'] ) ? count( $platform['questions'] ) : 0;
}

$inactive_count = max( 0, $platform_count - $active_count );
$max_cards      = 8;
$placeholder_qty = max( 1, $max_cards - $platform_count );

$plugin_pages = array(
	array(
		'label'       => __( 'Dashboard', 'coachpro-ai-teacher-social-branding' ),
		'description' => __( 'Overview and performance summary for all social platforms.', 'coachpro-ai-teacher-social-branding' ),
		'url'         => admin_url( 'admin.php?page=' . $this->plugin_name ),
		'type'        => __( 'Core', 'coachpro-ai-teacher-social-branding' ),
	),
);

foreach ( $platforms as $source_slug => $platform ) {
	$plugin_pages[] = array(
		'label'       => ! empty( $platform['name_en'] ) ? $platform['name_en'] : ucfirst( $source_slug ),
		'description' => __( 'Manage platform profile, prompt, and content questions.', 'coachpro-ai-teacher-social-branding' ),
		'url'         => admin_url( 'admin.php?page=' . $this->plugin_name . '-' . $source_slug ),
		'type'        => __( 'Platform', 'coachpro-ai-teacher-social-branding' ),
	);
}

$plugin_pages[] = array(
	'label'       => __( 'Data Import', 'coachpro-ai-teacher-social-branding' ),
	'description' => __( 'Import or export data with CSV and JSON options.', 'coachpro-ai-teacher-social-branding' ),
	'url'         => admin_url( 'admin.php?page=' . $this->plugin_name . '-data-import' ),
	'type'        => __( 'Tools', 'coachpro-ai-teacher-social-branding' ),
);

$plugin_pages[] = array(
	'label'       => __( 'Settings', 'coachpro-ai-teacher-social-branding' ),
	'description' => __( 'Configure global defaults and platform directory setup.', 'coachpro-ai-teacher-social-branding' ),
	'url'         => admin_url( 'admin.php?page=' . $this->plugin_name . '-settings' ),
	'type'        => __( 'Configuration', 'coachpro-ai-teacher-social-branding' ),
);

$page_count = count( $plugin_pages );
?>

<div class="cpai-tsb-admin-shell">
	<div class="cpai-tsb-dashboard-wrap">
		<section class="cpai-tsb-hero-panel">
			<div>
				<h1><?php esc_html_e( 'CoachPro Social Branding Dashboard', 'coachpro-ai-teacher-social-branding' ); ?></h1>
				<p><?php esc_html_e( 'A modern control center for your social platforms with clean, minimal and colorful insights.', 'coachpro-ai-teacher-social-branding' ); ?></p>
			</div>
			<a class="button button-primary cpai-tsb-hero-button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-settings' ) ); ?>">
				<?php esc_html_e( 'Open Settings', 'coachpro-ai-teacher-social-branding' ); ?>
			</a>
		</section>

		<section class="cpai-tsb-kpi-grid" aria-label="<?php esc_attr_e( 'Dashboard summary', 'coachpro-ai-teacher-social-branding' ); ?>">
			<article class="cpai-tsb-kpi-card">
				<p><?php esc_html_e( 'Total Platforms', 'coachpro-ai-teacher-social-branding' ); ?></p>
				<strong><?php echo esc_html( $platform_count ); ?></strong>
			</article>
			<article class="cpai-tsb-kpi-card">
				<p><?php esc_html_e( 'Active', 'coachpro-ai-teacher-social-branding' ); ?></p>
				<strong><?php echo esc_html( $active_count ); ?></strong>
			</article>
			<article class="cpai-tsb-kpi-card">
				<p><?php esc_html_e( 'Inactive', 'coachpro-ai-teacher-social-branding' ); ?></p>
				<strong><?php echo esc_html( $inactive_count ); ?></strong>
			</article>
			<article class="cpai-tsb-kpi-card">
				<p><?php esc_html_e( 'Total Questions', 'coachpro-ai-teacher-social-branding' ); ?></p>
				<strong><?php echo esc_html( $question_total ); ?></strong>
			</article>
			<article class="cpai-tsb-kpi-card">
				<p><?php esc_html_e( 'Plugin Pages', 'coachpro-ai-teacher-social-branding' ); ?></p>
				<strong><?php echo esc_html( $page_count ); ?></strong>
			</article>
		</section>

		<section class="cpai-tsb-page-directory" aria-label="<?php esc_attr_e( 'Plugin pages', 'coachpro-ai-teacher-social-branding' ); ?>">
			<div class="cpai-tsb-section-head">
				<h2><?php esc_html_e( 'All Branding System Pages', 'coachpro-ai-teacher-social-branding' ); ?></h2>
				<p><?php esc_html_e( 'Quick access cards for every page available in this plugin.', 'coachpro-ai-teacher-social-branding' ); ?></p>
			</div>

			<div class="cpai-tsb-page-grid" role="list">
				<?php foreach ( $plugin_pages as $page_item ) : ?>
					<a class="cpai-tsb-page-card" role="listitem" href="<?php echo esc_url( $page_item['url'] ); ?>">
						<span class="cpai-tsb-page-chip"><?php echo esc_html( $page_item['type'] ); ?></span>
						<h3><?php echo esc_html( $page_item['label'] ); ?></h3>
						<p><?php echo esc_html( $page_item['description'] ); ?></p>
						<span class="cpai-tsb-page-link"><?php esc_html_e( 'Open Page →', 'coachpro-ai-teacher-social-branding' ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>

		<div class="cpai-tsb-platform-grid" role="list" aria-label="<?php esc_attr_e( 'Platform cards', 'coachpro-ai-teacher-social-branding' ); ?>">
			<?php foreach ( $platforms as $source_slug => $platform ) : ?>
				<?php
				$question_count = isset( $platform['questions'] ) ? count( $platform['questions'] ) : 0;
				$manage_url     = admin_url( 'admin.php?page=' . $this->plugin_name . '-' . $source_slug );
				$card_title     = ! empty( $platform['name_en'] ) ? $platform['name_en'] : ucfirst( $source_slug );
				$logo_text      = ! empty( $platform['icon'] ) ? '' : strtoupper( substr( $card_title, 0, 2 ) );
				?>
				<article class="cpai-tsb-platform-card" role="listitem" style="background: linear-gradient(140deg, <?php echo esc_attr( $platform['light_color'] ); ?> 0%, <?php echo esc_attr( $platform['color'] ); ?> 100%);">
					<div class="cpai-tsb-platform-head">
						<span class="cpai-tsb-platform-logo" aria-hidden="true">
							<?php if ( ! empty( $platform['icon'] ) ) : ?>
								<i class="<?php echo esc_attr( $platform['icon'] ); ?>"></i>
							<?php else : ?>
								<?php echo esc_html( $logo_text ); ?>
							<?php endif; ?>
						</span>
						<div>
							<h2><?php echo esc_html( $card_title ); ?></h2>
							<p class="cpai-tsb-status">
								<span class="cpai-tsb-status-icon" aria-hidden="true">✓</span>
								<?php echo ! empty( $platform['enabled'] ) ? esc_html__( 'Active', 'coachpro-ai-teacher-social-branding' ) : esc_html__( 'Inactive', 'coachpro-ai-teacher-social-branding' ); ?>
							</p>
						</div>
					</div>

					<p class="cpai-tsb-questions">
						<span class="cpai-tsb-questions-count"><?php echo esc_html( $question_count ); ?></span>
						<?php esc_html_e( 'Questions', 'coachpro-ai-teacher-social-branding' ); ?>
					</p>

					<a class="button cpai-tsb-manage-button" href="<?php echo esc_url( $manage_url ); ?>">
						<?php esc_html_e( 'Manage Platform', 'coachpro-ai-teacher-social-branding' ); ?>
					</a>
				</article>
			<?php endforeach; ?>

			<?php for ( $index = 0; $index < $placeholder_qty; $index++ ) : ?>
				<a class="cpai-tsb-placeholder-card" role="listitem" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-settings#cpai-add-platform' ) ); ?>">
					<span>+</span>
					<p><?php esc_html_e( 'Add Platform', 'coachpro-ai-teacher-social-branding' ); ?></p>
				</a>
			<?php endfor; ?>
		</div>
	</div>
</div>
