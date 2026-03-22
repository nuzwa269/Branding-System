<div class="wrap">
	<h1><?php esc_html_e( 'Data Import & Export', 'coachpro-ai-teacher-social-branding' ); ?></h1>
	<p><?php esc_html_e( 'Import question data in bulk from CSV or JSON, or export current platform question data.', 'coachpro-ai-teacher-social-branding' ); ?></p>

	<h2><?php esc_html_e( 'Bulk Data Import', 'coachpro-ai-teacher-social-branding' ); ?></h2>
	<p><?php esc_html_e( 'Required columns: platform_name, question_en, question_ur, suggestion_title_en, suggestion_title_ur, suggestion_steps, tips, related_tool_placeholder. Optional columns: compare_left_image_url, compare_right_image_url, prompt_template_en, prompt_template_ur', 'coachpro-ai-teacher-social-branding' ); ?></p>

	<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="cpai_tsb_save_data" />
		<input type="hidden" name="cpai_tsb_action_type" value="import_data" />
		<?php wp_nonce_field( 'cpai_tsb_save_data', 'cpai_tsb_nonce' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Import Format', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<td>
					<label><input type="radio" name="import_format" value="csv" checked="checked" /> CSV</label>
					&nbsp;&nbsp;
					<label><input type="radio" name="import_format" value="json" /> JSON</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Import Mode', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<td>
					<label><input type="radio" name="import_mode" value="append" checked="checked" /> <?php esc_html_e( 'Append to existing questions', 'coachpro-ai-teacher-social-branding' ); ?></label><br />
					<label><input type="radio" name="import_mode" value="replace" /> <?php esc_html_e( 'Replace all existing questions', 'coachpro-ai-teacher-social-branding' ); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Data File', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<td><input type="file" name="import_file" accept=".csv,application/json,.json,text/csv" required /></td>
			</tr>
		</table>

		<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Run Import', 'coachpro-ai-teacher-social-branding' ); ?></button></p>
	</form>

	<hr />
	<h2><?php esc_html_e( 'Data Export', 'coachpro-ai-teacher-social-branding' ); ?></h2>
	<p><?php esc_html_e( 'Download all platform question data as CSV or JSON.', 'coachpro-ai-teacher-social-branding' ); ?></p>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="cpai_tsb_save_data" />
		<input type="hidden" name="cpai_tsb_action_type" value="export_data" />
		<?php wp_nonce_field( 'cpai_tsb_save_data', 'cpai_tsb_nonce' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Export Format', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<td>
					<label><input type="radio" name="export_format" value="csv" checked="checked" /> CSV</label>
					&nbsp;&nbsp;
					<label><input type="radio" name="export_format" value="json" /> JSON</label>
				</td>
			</tr>
		</table>

		<p class="submit"><button type="submit" class="button"><?php esc_html_e( 'Download Export File', 'coachpro-ai-teacher-social-branding' ); ?></button></p>
	</form>

	<hr />
	<h2><?php esc_html_e( 'Install Demo Data', 'coachpro-ai-teacher-social-branding' ); ?></h2>
	<p><?php esc_html_e( 'Quickly install platform-specific demo questions. Demo data is generated for the selected social platform only so content appears in the correct section.', 'coachpro-ai-teacher-social-branding' ); ?></p>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="cpai_tsb_save_data" />
		<input type="hidden" name="cpai_tsb_action_type" value="install_demo_data" />
		<?php wp_nonce_field( 'cpai_tsb_save_data', 'cpai_tsb_nonce' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Target Platform', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<td>
					<select name="demo_platform_slug" required>
						<?php foreach ( $platforms as $platform_slug => $platform ) : ?>
							<option value="<?php echo esc_attr( $platform_slug ); ?>"><?php echo esc_html( $platform['name_en'] ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Install Mode', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<td>
					<label><input type="radio" name="demo_import_mode" value="replace" checked="checked" /> <?php esc_html_e( 'Replace questions for selected platform', 'coachpro-ai-teacher-social-branding' ); ?></label><br />
					<label><input type="radio" name="demo_import_mode" value="append" /> <?php esc_html_e( 'Append demo questions to selected platform', 'coachpro-ai-teacher-social-branding' ); ?></label>
				</td>
			</tr>
		</table>

		<p class="submit"><button type="submit" class="button button-secondary"><?php esc_html_e( 'Install Demo Data', 'coachpro-ai-teacher-social-branding' ); ?></button></p>
	</form>

	<hr />
	<h2><?php esc_html_e( 'Supported Platforms', 'coachpro-ai-teacher-social-branding' ); ?></h2>
	<ul>
		<?php foreach ( $platforms as $platform ) : ?>
			<li><strong><?php echo esc_html( $platform['name_en'] ); ?></strong><?php echo ! empty( $platform['name_ur'] ) ? ' — ' . esc_html( $platform['name_ur'] ) : ''; ?></li>
		<?php endforeach; ?>
	</ul>
</div>
