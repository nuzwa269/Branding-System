<div class="wrap">
	<h1><?php echo esc_html( sprintf( __( '%s Controls', 'coachpro-ai-teacher-social-branding' ), $platform['name_en'] ) ); ?></h1>
	<p><?php esc_html_e( 'Edit this platform independently. Save changes before adding or deleting questions.', 'coachpro-ai-teacher-social-branding' ); ?></p>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="cpai_tsb_save_data" />
		<input type="hidden" name="cpai_tsb_action_type" value="save_platform" />
		<input type="hidden" name="platform_slug" value="<?php echo esc_attr( $platform_slug ); ?>" />
		<?php wp_nonce_field( 'cpai_tsb_save_data', 'cpai_tsb_nonce' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Enable Platform', 'coachpro-ai-teacher-social-branding' ); ?></th>
				<td><label><input type="checkbox" name="platform[enabled]" value="1" <?php checked( ! empty( $platform['enabled'] ) ); ?> /> <?php esc_html_e( 'Show this platform in frontend flow', 'coachpro-ai-teacher-social-branding' ); ?></label></td>
			</tr>
			<tr><th scope="row"><?php esc_html_e( 'Title', 'coachpro-ai-teacher-social-branding' ); ?></th><td><input type="text" class="regular-text" name="platform[title]" value="<?php echo esc_attr( $platform['title'] ); ?>" /></td></tr>
			<tr><th scope="row"><?php esc_html_e( 'Description', 'coachpro-ai-teacher-social-branding' ); ?></th><td><textarea class="large-text" rows="3" name="platform[description]"><?php echo esc_textarea( $platform['description'] ); ?></textarea></td></tr>
			<tr><th scope="row"><?php esc_html_e( 'Theme Color', 'coachpro-ai-teacher-social-branding' ); ?></th><td><input type="color" name="platform[color]" value="<?php echo esc_attr( $platform['color'] ); ?>" /></td></tr>
			<tr><th scope="row"><?php esc_html_e( 'Button Label', 'coachpro-ai-teacher-social-branding' ); ?></th><td><input type="text" class="regular-text" name="platform[button_label]" value="<?php echo esc_attr( $platform['button_label'] ); ?>" /></td></tr>
		</table>

		<hr />
		<h2><?php esc_html_e( 'Question Bank', 'coachpro-ai-teacher-social-branding' ); ?></h2>

		<?php foreach ( $platform['questions'] as $index => $question ) : ?>
			<div style="background:#fff;padding:16px;margin:0 0 12px;border:1px solid #ccd0d4;">
				<h3><?php echo esc_html( sprintf( __( 'Question %d', 'coachpro-ai-teacher-social-branding' ), $index + 1 ) ); ?></h3>
				<input type="hidden" name="platform[questions][<?php echo esc_attr( $index ); ?>][id]" value="<?php echo esc_attr( $question['id'] ); ?>" />
				<p><label><?php esc_html_e( 'Question Text (EN)', 'coachpro-ai-teacher-social-branding' ); ?></label><input type="text" class="widefat" name="platform[questions][<?php echo esc_attr( $index ); ?>][text_en]" value="<?php echo esc_attr( $question['text_en'] ); ?>" /></p>
				<p><label><?php esc_html_e( 'Question Text (UR)', 'coachpro-ai-teacher-social-branding' ); ?></label><input type="text" class="widefat" dir="rtl" name="platform[questions][<?php echo esc_attr( $index ); ?>][text_ur]" value="<?php echo esc_attr( $question['text_ur'] ); ?>" /></p>
				<p><label><?php esc_html_e( 'Negative Title (EN)', 'coachpro-ai-teacher-social-branding' ); ?></label><input type="text" class="widefat" name="platform[questions][<?php echo esc_attr( $index ); ?>][instruction_en][title]" value="<?php echo esc_attr( $question['instruction_en']['title'] ); ?>" /></p>
				<p><label><?php esc_html_e( 'Negative Steps (EN, one per line)', 'coachpro-ai-teacher-social-branding' ); ?></label><textarea class="widefat" rows="3" name="platform[questions][<?php echo esc_attr( $index ); ?>][instruction_en][steps]"><?php echo esc_textarea( implode( "\n", $question['instruction_en']['steps'] ) ); ?></textarea></p>
				<p><label><?php esc_html_e( 'Tips (EN, one per line)', 'coachpro-ai-teacher-social-branding' ); ?></label><textarea class="widefat" rows="3" name="platform[questions][<?php echo esc_attr( $index ); ?>][instruction_en][tips]"><?php echo esc_textarea( implode( "\n", $question['instruction_en']['tips'] ) ); ?></textarea></p>
				<p><label><?php esc_html_e( 'Negative Title (UR)', 'coachpro-ai-teacher-social-branding' ); ?></label><input type="text" class="widefat" dir="rtl" name="platform[questions][<?php echo esc_attr( $index ); ?>][instruction_ur][title]" value="<?php echo esc_attr( $question['instruction_ur']['title'] ); ?>" /></p>
				<p><label><?php esc_html_e( 'Negative Steps (UR, one per line)', 'coachpro-ai-teacher-social-branding' ); ?></label><textarea class="widefat" dir="rtl" rows="3" name="platform[questions][<?php echo esc_attr( $index ); ?>][instruction_ur][steps]"><?php echo esc_textarea( implode( "\n", $question['instruction_ur']['steps'] ) ); ?></textarea></p>
				<p><label><?php esc_html_e( 'Tips (UR, one per line)', 'coachpro-ai-teacher-social-branding' ); ?></label><textarea class="widefat" dir="rtl" rows="3" name="platform[questions][<?php echo esc_attr( $index ); ?>][instruction_ur][tips]"><?php echo esc_textarea( implode( "\n", $question['instruction_ur']['tips'] ) ); ?></textarea></p>
				<p><label><?php esc_html_e( 'Related Tool Placeholder (EN HTML/Embed)', 'coachpro-ai-teacher-social-branding' ); ?></label><textarea class="widefat" rows="2" name="platform[questions][<?php echo esc_attr( $index ); ?>][instruction_en][tool]"><?php echo esc_textarea( $question['instruction_en']['tool'] ); ?></textarea></p>
				<p><label><?php esc_html_e( 'Related Tool Placeholder (UR HTML/Embed)', 'coachpro-ai-teacher-social-branding' ); ?></label><textarea class="widefat" rows="2" dir="rtl" name="platform[questions][<?php echo esc_attr( $index ); ?>][instruction_ur][tool]"><?php echo esc_textarea( $question['instruction_ur']['tool'] ); ?></textarea></p>
			</div>
		<?php endforeach; ?>

		<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Save Platform', 'coachpro-ai-teacher-social-branding' ); ?></button></p>
	</form>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;margin-right:8px;">
		<input type="hidden" name="action" value="cpai_tsb_save_data" />
		<input type="hidden" name="cpai_tsb_action_type" value="add_question" />
		<input type="hidden" name="platform_slug" value="<?php echo esc_attr( $platform_slug ); ?>" />
		<?php wp_nonce_field( 'cpai_tsb_save_data', 'cpai_tsb_nonce' ); ?>
		<button type="submit" class="button"><?php esc_html_e( 'Add Question', 'coachpro-ai-teacher-social-branding' ); ?></button>
	</form>

	<?php if ( ! empty( $platform['questions'] ) ) : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;">
			<input type="hidden" name="action" value="cpai_tsb_save_data" />
			<input type="hidden" name="cpai_tsb_action_type" value="delete_question" />
			<input type="hidden" name="platform_slug" value="<?php echo esc_attr( $platform_slug ); ?>" />
			<input type="hidden" name="question_index" value="<?php echo esc_attr( count( $platform['questions'] ) - 1 ); ?>" />
			<?php wp_nonce_field( 'cpai_tsb_save_data', 'cpai_tsb_nonce' ); ?>
			<button type="submit" class="button button-secondary"><?php esc_html_e( 'Delete Last Question', 'coachpro-ai-teacher-social-branding' ); ?></button>
		</form>
	<?php endif; ?>
</div>
