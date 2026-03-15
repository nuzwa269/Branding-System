<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    CoachPro_AI_Social_Branding
 * @subpackage CoachPro_AI_Social_Branding/admin
 */
class CPAI_TSB_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name . '-admin',
			plugin_dir_url( __FILE__ ) . 'css/cpai-tsb-admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	public function enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_script(
			$this->plugin_name . '-admin',
			plugin_dir_url( __FILE__ ) . 'js/cpai-tsb-admin.js',
			array( 'jquery' ),
			$this->version,
			true
		);
	}

	public function add_plugin_admin_menu() {
		$capability = 'manage_options';
		$platforms  = $this->get_platforms();

		add_menu_page(
			__( 'CoachPro AI Social Branding', 'coachpro-ai-teacher-social-branding' ),
			__( 'Social Branding', 'coachpro-ai-teacher-social-branding' ),
			$capability,
			$this->plugin_name,
			array( $this, 'display_dashboard_page' ),
			'dashicons-share',
			58
		);

		add_submenu_page(
			$this->plugin_name,
			__( 'Dashboard', 'coachpro-ai-teacher-social-branding' ),
			__( 'Dashboard', 'coachpro-ai-teacher-social-branding' ),
			$capability,
			$this->plugin_name,
			array( $this, 'display_dashboard_page' )
		);

		foreach ( $platforms as $platform_slug => $platform ) {
			$platform_title = ! empty( $platform['name_en'] ) ? $platform['name_en'] : ucfirst( $platform_slug );
			add_submenu_page(
				$this->plugin_name,
				sprintf( __( '%s Controls', 'coachpro-ai-teacher-social-branding' ), $platform_title ),
				$platform_title,
				$capability,
				$this->plugin_name . '-' . $platform_slug,
				array( $this, 'display_platform_page' )
			);
		}

		add_submenu_page(
			$this->plugin_name,
			__( 'Data Import', 'coachpro-ai-teacher-social-branding' ),
			__( 'Data Import', 'coachpro-ai-teacher-social-branding' ),
			$capability,
			$this->plugin_name . '-data-import',
			array( $this, 'display_data_import_page' )
		);

		add_submenu_page(
			$this->plugin_name,
			__( 'Settings', 'coachpro-ai-teacher-social-branding' ),
			__( 'Settings', 'coachpro-ai-teacher-social-branding' ),
			$capability,
			$this->plugin_name . '-settings',
			array( $this, 'display_settings_page' )
		);
	}

	public function display_dashboard_page() {
		$this->render_admin_page(
			'page-dashboard.php',
			array(
				'platforms' => $this->get_platforms(),
			)
		);
	}

	public function display_platform_page() {
		$platform_slug = isset( $_GET['page'] ) ? sanitize_key( str_replace( $this->plugin_name . '-', '', wp_unslash( $_GET['page'] ) ) ) : '';
		$platforms     = $this->get_platforms();

		if ( ! isset( $platforms[ $platform_slug ] ) ) {
			wp_die( esc_html__( 'Invalid platform.', 'coachpro-ai-teacher-social-branding' ) );
		}

		$this->render_admin_page(
			'page-platform.php',
			array(
				'platform_slug' => $platform_slug,
				'platform'      => $platforms[ $platform_slug ],
			)
		);
	}

	public function display_settings_page() {
		$this->render_admin_page(
			'page-settings.php',
			array(
				'settings'  => $this->get_settings(),
				'platforms' => $this->get_platforms(),
			)
		);
	}

	public function display_data_import_page() {
		$this->render_admin_page(
			'page-data-import.php',
			array(
				'platforms' => $this->get_platforms(),
			)
		);
	}

	public function save_data() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'coachpro-ai-teacher-social-branding' ) );
		}

		if ( ! isset( $_POST['cpai_tsb_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cpai_tsb_nonce'] ) ), 'cpai_tsb_save_data' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'coachpro-ai-teacher-social-branding' ) );
		}

		$action        = isset( $_POST['cpai_tsb_action_type'] ) ? sanitize_key( wp_unslash( $_POST['cpai_tsb_action_type'] ) ) : '';
		$platform_slug = isset( $_POST['platform_slug'] ) ? sanitize_key( wp_unslash( $_POST['platform_slug'] ) ) : '';

		switch ( $action ) {
			case 'save_platform':
				$this->handle_save_platform( $platform_slug );
				$this->redirect_with_message( $this->plugin_name . '-' . $platform_slug, 'platform_saved' );
				break;
			case 'add_question':
				$this->handle_add_question( $platform_slug );
				$this->redirect_with_message( $this->plugin_name . '-' . $platform_slug, 'question_added' );
				break;
			case 'delete_question':
				$question_index = isset( $_POST['question_index'] ) ? absint( wp_unslash( $_POST['question_index'] ) ) : -1;
				$this->handle_delete_question( $platform_slug, $question_index );
				$this->redirect_with_message( $this->plugin_name . '-' . $platform_slug, 'question_deleted' );
				break;
			case 'save_settings':
				$this->handle_save_settings();
				$this->redirect_with_message( $this->plugin_name . '-settings', 'settings_saved' );
				break;
			case 'save_platform_directory':
				$this->handle_save_platform_directory();
				$this->redirect_with_message( $this->plugin_name . '-settings', 'platforms_saved' );
				break;
			case 'add_platform':
				$this->handle_add_platform();
				$this->redirect_with_message( $this->plugin_name . '-settings', 'platform_added' );
				break;
			case 'import_data':
				$result = $this->handle_import_data();
				$this->redirect_with_message( $this->plugin_name . '-data-import', $result );
				break;
			case 'install_demo_data':
				$result = $this->handle_install_demo_data();
				$this->redirect_with_message( $this->plugin_name . '-data-import', $result );
				break;
			case 'export_data':
				$this->handle_export_data();
				break;
			default:
				$this->redirect_with_message( $this->plugin_name, 'invalid_action' );
				break;
		}
	}

	private function handle_import_data() {
		$format = isset( $_POST['import_format'] ) ? sanitize_key( wp_unslash( $_POST['import_format'] ) ) : '';
		$mode   = isset( $_POST['import_mode'] ) ? sanitize_key( wp_unslash( $_POST['import_mode'] ) ) : 'append';

		if ( ! in_array( $format, array( 'csv', 'json' ), true ) ) {
			return 'import_invalid_format';
		}

		if ( ! isset( $_FILES['import_file'] ) || empty( $_FILES['import_file']['tmp_name'] ) || ! is_uploaded_file( $_FILES['import_file']['tmp_name'] ) ) {
			return 'import_missing_file';
		}

		$rows = 'csv' === $format
			? $this->parse_import_csv( $_FILES['import_file']['tmp_name'] )
			: $this->parse_import_json( $_FILES['import_file']['tmp_name'] );

		if ( empty( $rows ) || ! is_array( $rows ) ) {
			return 'import_no_rows';
		}

		$platforms = $this->get_platforms();

		if ( 'replace' === $mode ) {
			foreach ( $platforms as $slug => $platform ) {
				$platforms[ $slug ]['questions'] = array();
			}
		}

		$imported_count = 0;
		$skipped_count  = 0;

		foreach ( $rows as $position => $row ) {
			$normalized = $this->normalize_import_row( $row );

			if ( empty( $normalized['platform_name'] ) || empty( $normalized['question_en'] ) || empty( $normalized['suggestion_title_en'] ) ) {
				++$skipped_count;
				continue;
			}

			$platform_slug = $this->resolve_platform_slug( $normalized['platform_name'], $platforms );
			if ( empty( $platform_slug ) ) {
				++$skipped_count;
				continue;
			}

			if ( ! isset( $platforms[ $platform_slug ] ) ) {
				$platforms[ $platform_slug ] = $this->build_platform_defaults(
					$platform_slug,
					$normalized['platform_name'],
					'#2563eb',
					sprintf( 'Analyze %s', $normalized['platform_name'] )
				);
				$platforms[ $platform_slug ]['questions'] = array();
			}

			$platforms[ $platform_slug ]['questions'][] = $this->normalize_question_payload(
				array(
					'id'      => 'q' . ( count( $platforms[ $platform_slug ]['questions'] ) + 1 ),
					'text_en' => $normalized['question_en'],
					'text_ur' => $normalized['question_ur'],
					'compare_left_image_url' => $normalized['compare_left_image_url'],
					'compare_right_image_url' => $normalized['compare_right_image_url'],
					'instruction_en' => array(
						'title' => $normalized['suggestion_title_en'],
						'steps' => $normalized['suggestion_steps'],
						'tips'  => $normalized['tips'],
						'tool'  => $normalized['related_tool_placeholder'],
					),
					'instruction_ur' => array(
						'title' => $normalized['suggestion_title_ur'],
						'steps' => $normalized['suggestion_steps'],
						'tips'  => $normalized['tips'],
						'tool'  => $normalized['related_tool_placeholder'],
					),
				),
				$position + 1
			);

			++$imported_count;
		}

		foreach ( $platforms as $slug => $platform ) {
			$platforms[ $slug ] = $this->normalize_platform_payload( $platform, $slug );
		}

		update_option( 'cpai_tsb_platforms', $platforms );

		if ( 0 === $imported_count ) {
			return 'import_no_valid_rows';
		}

		return $skipped_count > 0 ? 'imported_with_skips' : 'import_success';
	}

	private function handle_export_data() {
		$format    = isset( $_POST['export_format'] ) ? sanitize_key( wp_unslash( $_POST['export_format'] ) ) : 'json';
		$platforms = $this->get_platforms();

		$rows = array();
		foreach ( $platforms as $platform ) {
			foreach ( $platform['questions'] as $question ) {
				$rows[] = array(
					'platform_name'             => $platform['name_en'],
					'question_en'               => $question['text_en'],
					'question_ur'               => $question['text_ur'],
					'compare_left_image_url'    => isset( $question['compare_left_image_url'] ) ? $question['compare_left_image_url'] : '',
					'compare_right_image_url'   => isset( $question['compare_right_image_url'] ) ? $question['compare_right_image_url'] : '',
					'suggestion_title_en'       => $question['instruction_en']['title'],
					'suggestion_title_ur'       => $question['instruction_ur']['title'],
					'suggestion_steps'          => implode( ' | ', $question['instruction_en']['steps'] ),
					'tips'                      => implode( ' | ', $question['instruction_en']['tips'] ),
					'related_tool_placeholder'  => $question['instruction_en']['tool'],
				);
			}
		}

		$filename_base = 'cpai-tsb-platform-data-' . gmdate( 'Ymd-His' );

		if ( 'csv' === $format ) {
			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=' . $filename_base . '.csv' );

			$output = fopen( 'php://output', 'w' );
			fputcsv( $output, array( 'platform_name', 'question_en', 'question_ur', 'compare_left_image_url', 'compare_right_image_url', 'suggestion_title_en', 'suggestion_title_ur', 'suggestion_steps', 'tips', 'related_tool_placeholder' ) );
			foreach ( $rows as $row ) {
				fputcsv( $output, $row );
			}
			fclose( $output );
			exit;
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $filename_base . '.json' );
		echo wp_json_encode( $rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		exit;
	}

	private function handle_install_demo_data() {
		$target_platform = isset( $_POST['demo_platform_slug'] ) ? sanitize_key( wp_unslash( $_POST['demo_platform_slug'] ) ) : '';
		$mode            = isset( $_POST['demo_import_mode'] ) ? sanitize_key( wp_unslash( $_POST['demo_import_mode'] ) ) : 'replace';

		$platforms = $this->get_platforms();

		if ( empty( $target_platform ) || ! isset( $platforms[ $target_platform ] ) ) {
			return 'demo_invalid_platform';
		}

		if ( 'replace' === $mode ) {
			$platforms[ $target_platform ]['questions'] = array();
		}

		$demo_rows      = $this->build_demo_rows_for_platform( $platforms[ $target_platform ] );
		$starting_index = count( $platforms[ $target_platform ]['questions'] );

		foreach ( $demo_rows as $offset => $row ) {
			$platforms[ $target_platform ]['questions'][] = $this->normalize_question_payload(
				array(
					'id'      => 'q' . ( $starting_index + $offset + 1 ),
					'text_en' => $row['question_en'],
					'text_ur' => $row['question_ur'],
					'instruction_en' => array(
						'title' => $row['suggestion_title_en'],
						'steps' => $row['suggestion_steps'],
						'tips'  => $row['tips'],
						'tool'  => $row['related_tool_placeholder'],
					),
					'instruction_ur' => array(
						'title' => $row['suggestion_title_ur'],
						'steps' => $row['suggestion_steps_ur'],
						'tips'  => $row['tips_ur'],
						'tool'  => $row['related_tool_placeholder_ur'],
					),
				),
				$starting_index + $offset + 1
			);
		}

		$platforms[ $target_platform ] = $this->normalize_platform_payload( $platforms[ $target_platform ], $target_platform );

		update_option( 'cpai_tsb_platforms', $platforms );

		return 'demo_data_installed';
	}

	private function build_demo_rows_for_platform( $platform ) {
		$platform_name_en = ! empty( $platform['name_en'] ) ? sanitize_text_field( $platform['name_en'] ) : __( 'Platform', 'coachpro-ai-teacher-social-branding' );
		$platform_name_ur = ! empty( $platform['name_ur'] ) ? sanitize_text_field( $platform['name_ur'] ) : $platform_name_en;

		return array(
			array(
				'question_en'                   => sprintf( 'Does your %s profile photo look clear, professional, and consistent with your teaching brand?', $platform_name_en ),
				'question_ur'                   => sprintf( 'کیا %s کی پروفائل تصویر واضح، پروفیشنل اور آپ کی تدریسی برانڈنگ سے ہم آہنگ ہے؟', $platform_name_ur ),
				'suggestion_title_en'           => 'Refresh your profile identity',
				'suggestion_title_ur'           => 'پروفائل شناخت کو بہتر بنائیں',
				'suggestion_steps'              => array(
					'Use a clean headshot with good lighting and neutral background.',
					'Keep the same visual style across profile, banner, and posts.',
					'Avoid clutter and keep the main face area clearly visible.',
				),
				'suggestion_steps_ur'           => array(
					'صاف پس منظر اور اچھی روشنی کے ساتھ واضح تصویر استعمال کریں۔',
					'پروفائل، بینر اور پوسٹس میں یکساں برانڈ اسٹائل رکھیں۔',
					'غیر ضروری عناصر ہٹا کر چہرہ نمایاں رکھیں۔',
				),
				'tips'                          => array( 'Educator accounts gain more trust when the profile identity is consistent and personal.' ),
				'tips_ur'                       => array( 'استاد کے اکاؤنٹس میں مسلسل اور ذاتی پروفائل شناخت اعتماد بڑھاتی ہے۔' ),
				'related_tool_placeholder'      => '<em>Tool idea:</em> Canva profile photo template.',
				'related_tool_placeholder_ur'   => '<em>ٹول آئیڈیا:</em> Canva پروفائل فوٹو ٹیمپلیٹ۔',
			),
			array(
				'question_en'                   => sprintf( 'Is your %s bio written around student outcomes instead of generic teacher wording?', $platform_name_en ),
				'question_ur'                   => sprintf( 'کیا %s کا بایو عام جملوں کے بجائے طالب علم کے نتائج پر مبنی ہے؟', $platform_name_ur ),
				'suggestion_title_en'           => 'Rewrite your bio for conversion',
				'suggestion_title_ur'           => 'بایو کو نتیجہ خیز بنائیں',
				'suggestion_steps'              => array(
					'Start with who you teach and what result they can expect.',
					'Add one proof point (years, grades improved, or success stories).',
					'End with one clear next step like message or booking link.',
				),
				'suggestion_steps_ur'           => array(
					'واضح کریں کہ آپ کن طلبہ کو پڑھاتے ہیں اور کیا نتیجہ دیتے ہیں۔',
					'ایک مضبوط ثبوت شامل کریں (تجربہ، نتائج یا کامیابی کہانی)۔',
					'آخر میں رابطے کا واضح اگلا قدم دیں۔',
				),
				'tips'                          => array( 'Outcome-based bios attract serious students faster than generic introductions.' ),
				'tips_ur'                       => array( 'نتیجہ پر مبنی بایو عام تعارف سے زیادہ سنجیدہ طلبہ کو متوجہ کرتا ہے۔' ),
				'related_tool_placeholder'      => '<em>Tool idea:</em> Bio rewrite prompt template.',
				'related_tool_placeholder_ur'   => '<em>ٹول آئیڈیا:</em> بایو ری رائٹ پرامپٹ ٹیمپلیٹ۔',
			),
			array(
				'question_en'                   => sprintf( 'Are your %s posts using clear hooks and a strong call-to-action?', $platform_name_en ),
				'question_ur'                   => sprintf( 'کیا %s کی پوسٹس میں واضح ہُک اور مضبوط کال ٹو ایکشن شامل ہے؟', $platform_name_ur ),
				'suggestion_title_en'           => 'Improve content structure',
				'suggestion_title_ur'           => 'کنٹینٹ اسٹرکچر بہتر کریں',
				'suggestion_steps'              => array(
					'Open with a pain-point question in the first line.',
					'Share one practical mini-tip with simple language.',
					'Close with one CTA: comment, message, or click.',
				),
				'suggestion_steps_ur'           => array(
					'پہلی لائن میں مسئلہ پر مبنی سوال لکھیں۔',
					'سادہ زبان میں ایک عملی ٹِپ دیں۔',
					'آخر میں ایک واضح CTA دیں: کمنٹ، میسج یا کلک۔',
				),
				'tips'                          => array( 'A predictable content format improves audience retention and response rate.' ),
				'tips_ur'                       => array( 'مسلسل کنٹینٹ فارمیٹ سے آڈینس ریٹینشن اور رسپانس بہتر ہوتا ہے۔' ),
				'related_tool_placeholder'      => '<em>Tool idea:</em> Weekly content planner.',
				'related_tool_placeholder_ur'   => '<em>ٹول آئیڈیا:</em> ویکلی کنٹینٹ پلینر۔',
			),
			array(
				'question_en'                   => sprintf( 'Do you publish social proof on %s (student results, testimonials, or feedback) every week?', $platform_name_en ),
				'question_ur'                   => sprintf( 'کیا آپ %s پر ہر ہفتے سوشل پروف (نتائج، ٹیسٹی مونیلز، یا فیڈبیک) شیئر کرتے ہیں؟', $platform_name_ur ),
				'suggestion_title_en'           => 'Systemize your social proof',
				'suggestion_title_ur'           => 'سوشل پروف کو منظم کریں',
				'suggestion_steps'              => array(
					'Collect permission-based student feedback screenshots.',
					'Turn one result into a short story post with context.',
					'Create a weekly slot for trust-building proof content.',
				),
				'suggestion_steps_ur'           => array(
					'طلبہ کے فیڈبیک اسکرین شاٹس اجازت کے ساتھ جمع کریں۔',
					'نتیجے کو مختصر کہانی پوسٹ میں تبدیل کریں۔',
					'اعتماد بڑھانے والے مواد کے لیے ہفتہ وار شیڈول بنائیں۔',
				),
				'tips'                          => array( 'Consistent proof-based content raises credibility and inquiries.' ),
				'tips_ur'                       => array( 'مسلسل ثبوت پر مبنی مواد سے ساکھ اور انکوائریز میں اضافہ ہوتا ہے۔' ),
				'related_tool_placeholder'      => '<em>Tool idea:</em> Testimonial carousel template.',
				'related_tool_placeholder_ur'   => '<em>ٹول آئیڈیا:</em> ٹیسٹی مونیل کیروسل ٹیمپلیٹ۔',
			),
			array(
				'question_en'                   => sprintf( 'Are you reviewing your %s analytics monthly to improve content performance?', $platform_name_en ),
				'question_ur'                   => sprintf( 'کیا آپ %s کی اینالیٹکس ماہانہ چیک کر کے کنٹینٹ پرفارمنس بہتر کرتے ہیں؟', $platform_name_ur ),
				'suggestion_title_en'           => 'Track and optimize with data',
				'suggestion_title_ur'           => 'ڈیٹا سے بہتری لائیں',
				'suggestion_steps'              => array(
					'Review top-performing posts by reach and engagement.',
					'Identify the best posting times and repeat winning formats.',
					'Remove low-performing content types from next month plan.',
				),
				'suggestion_steps_ur'           => array(
					'ریچ اور انگیجمنٹ کے مطابق بہترین پوسٹس کا جائزہ لیں۔',
					'بہترین پوسٹنگ اوقات اور کامیاب فارمیٹس دہرائیں۔',
					'کمزور مواد کو اگلے مہینے کے پلان سے نکال دیں۔',
				),
				'tips'                          => array( 'Small monthly optimization loops create compounding growth over time.' ),
				'tips_ur'                       => array( 'ماہانہ چھوٹی بہتریاں وقت کے ساتھ بڑا اور مستقل گروتھ دیتی ہیں۔' ),
				'related_tool_placeholder'      => '<em>Tool idea:</em> Monthly analytics review sheet.',
				'related_tool_placeholder_ur'   => '<em>ٹول آئیڈیا:</em> ماہانہ اینالیٹکس ریویو شیٹ۔',
			),
		);
	}

	private function normalize_import_row( $row ) {
		$row = is_array( $row ) ? $row : array();

		return array(
			'platform_name'             => isset( $row['platform_name'] ) ? sanitize_text_field( $row['platform_name'] ) : '',
			'question_en'               => isset( $row['question_en'] ) ? sanitize_text_field( $row['question_en'] ) : '',
			'question_ur'               => isset( $row['question_ur'] ) ? sanitize_text_field( $row['question_ur'] ) : '',
			'compare_left_image_url'    => isset( $row['compare_left_image_url'] ) ? esc_url_raw( $row['compare_left_image_url'] ) : '',
			'compare_right_image_url'   => isset( $row['compare_right_image_url'] ) ? esc_url_raw( $row['compare_right_image_url'] ) : '',
			'suggestion_title_en'       => isset( $row['suggestion_title_en'] ) ? sanitize_text_field( $row['suggestion_title_en'] ) : '',
			'suggestion_title_ur'       => isset( $row['suggestion_title_ur'] ) ? sanitize_text_field( $row['suggestion_title_ur'] ) : '',
			'suggestion_steps'          => $this->sanitize_lines( isset( $row['suggestion_steps'] ) ? $row['suggestion_steps'] : '' ),
			'tips'                      => $this->sanitize_lines( isset( $row['tips'] ) ? $row['tips'] : '' ),
			'related_tool_placeholder'  => isset( $row['related_tool_placeholder'] ) ? wp_kses_post( $row['related_tool_placeholder'] ) : '',
		);
	}

	private function resolve_platform_slug( $platform_name, $platforms ) {
		$sanitized_name = sanitize_text_field( $platform_name );
		$direct_slug    = sanitize_key( $sanitized_name );

		if ( isset( $platforms[ $direct_slug ] ) ) {
			return $direct_slug;
		}

		foreach ( $platforms as $slug => $platform ) {
			if ( 0 === strcasecmp( $sanitized_name, $platform['name_en'] ) || ( ! empty( $platform['name_ur'] ) && 0 === strcasecmp( $sanitized_name, $platform['name_ur'] ) ) ) {
				return $slug;
			}
		}

		return $direct_slug;
	}

	private function parse_import_json( $path ) {
		$raw = file_get_contents( $path );
		if ( false === $raw || '' === trim( $raw ) ) {
			return array();
		}

		$decoded = json_decode( $raw, true );
		if ( JSON_ERROR_NONE !== json_last_error() ) {
			return array();
		}

		if ( isset( $decoded['rows'] ) && is_array( $decoded['rows'] ) ) {
			return $decoded['rows'];
		}

		return is_array( $decoded ) ? $decoded : array();
	}

	private function parse_import_csv( $path ) {
		$handle = fopen( $path, 'r' );
		if ( ! $handle ) {
			return array();
		}

		$headers = fgetcsv( $handle );
		if ( ! is_array( $headers ) ) {
			fclose( $handle );
			return array();
		}

		$headers = array_map(
			static function ( $header ) {
				$header = is_string( $header ) ? $header : '';
				$header = preg_replace( '/^\xEF\xBB\xBF/', '', $header );
				return sanitize_key( str_replace( ' ', '_', strtolower( trim( $header ) ) ) );
			},
			$headers
		);

		$rows = array();
		while ( false !== ( $row = fgetcsv( $handle ) ) ) {
			if ( empty( array_filter( $row ) ) ) {
				continue;
			}

			$rows[] = array_combine( $headers, array_pad( $row, count( $headers ), '' ) );
		}

		fclose( $handle );
		return $rows;
	}

	private function handle_save_platform( $platform_slug ) {
		$platforms = $this->get_platforms();
		if ( ! isset( $platforms[ $platform_slug ] ) ) {
			return;
		}

		$posted_platform = isset( $_POST['platform'] ) ? wp_unslash( $_POST['platform'] ) : array();
		$platform        = $platforms[ $platform_slug ];

		$platform['enabled']      = isset( $posted_platform['enabled'] ) ? 1 : 0;
		$platform['title']        = isset( $posted_platform['title'] ) ? sanitize_text_field( $posted_platform['title'] ) : $platform['title'];
		$platform['description']  = isset( $posted_platform['description'] ) ? sanitize_textarea_field( $posted_platform['description'] ) : $platform['description'];
		$platform['color']        = isset( $posted_platform['color'] ) ? ( sanitize_hex_color( $posted_platform['color'] ) ?: $platform['color'] ) : $platform['color'];
		$platform['light_color']  = isset( $posted_platform['light_color'] ) ? ( sanitize_hex_color( $posted_platform['light_color'] ) ?: $platform['light_color'] ) : $platform['light_color'];
		$platform['button_label'] = isset( $posted_platform['button_label'] ) ? sanitize_text_field( $posted_platform['button_label'] ) : $platform['button_label'];

		$questions              = isset( $posted_platform['questions'] ) && is_array( $posted_platform['questions'] ) ? $posted_platform['questions'] : array();
		$platform['questions']  = array();
		foreach ( $questions as $question_index => $question ) {
			$platform['questions'][] = $this->normalize_question_payload( $question, $question_index + 1 );
		}

		if ( empty( $platform['questions'] ) ) {
			$platform['questions'][] = $this->get_empty_question( 1 );
		}

		$platforms[ $platform_slug ] = $this->normalize_platform_payload( $platform, $platform_slug );
		update_option( 'cpai_tsb_platforms', $platforms );
	}

	private function handle_add_question( $platform_slug ) {
		$platforms = $this->get_platforms();
		if ( ! isset( $platforms[ $platform_slug ] ) ) {
			return;
		}

		$platforms[ $platform_slug ]['questions'][] = $this->get_empty_question( count( $platforms[ $platform_slug ]['questions'] ) + 1 );
		update_option( 'cpai_tsb_platforms', $platforms );
	}

	private function handle_delete_question( $platform_slug, $question_index ) {
		$platforms = $this->get_platforms();
		if ( ! isset( $platforms[ $platform_slug ]['questions'][ $question_index ] ) ) {
			return;
		}

		unset( $platforms[ $platform_slug ]['questions'][ $question_index ] );
		$platforms[ $platform_slug ]['questions'] = array_values( $platforms[ $platform_slug ]['questions'] );
		update_option( 'cpai_tsb_platforms', $platforms );
	}

	private function handle_save_settings() {
		$posted_settings = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : array();
		$settings        = $this->get_settings();

		$settings['default_language'] = isset( $posted_settings['default_language'] ) && in_array( $posted_settings['default_language'], array( 'en', 'ur' ), true ) ? $posted_settings['default_language'] : 'en';
		$settings['show_branding']    = isset( $posted_settings['show_branding'] ) ? 1 : 0;
		$settings['items_per_page']   = isset( $posted_settings['items_per_page'] ) ? max( 1, absint( $posted_settings['items_per_page'] ) ) : 10;
		$settings['dashboard_title']  = isset( $posted_settings['dashboard_title'] ) ? sanitize_text_field( $posted_settings['dashboard_title'] ) : $settings['dashboard_title'];
		$settings['primary_color']    = isset( $posted_settings['primary_color'] ) ? sanitize_hex_color( $posted_settings['primary_color'] ) : $settings['primary_color'];

		update_option( 'cpai_tsb_settings', $settings );
	}

	private function handle_save_platform_directory() {
		$platforms         = $this->get_platforms();
		$posted_platforms  = isset( $_POST['platforms'] ) && is_array( $_POST['platforms'] ) ? wp_unslash( $_POST['platforms'] ) : array();

		foreach ( $posted_platforms as $slug => $posted_platform ) {
			$slug = sanitize_key( $slug );
			if ( ! isset( $platforms[ $slug ] ) ) {
				continue;
			}

			$existing                      = $platforms[ $slug ];
			$existing['name_en']           = isset( $posted_platform['name_en'] ) ? sanitize_text_field( $posted_platform['name_en'] ) : $existing['name_en'];
			$existing['name_ur']           = isset( $posted_platform['name_ur'] ) ? sanitize_text_field( $posted_platform['name_ur'] ) : $existing['name_ur'];
			$existing['icon']              = isset( $posted_platform['icon'] ) ? sanitize_text_field( $posted_platform['icon'] ) : $existing['icon'];
			$existing['enabled']           = isset( $posted_platform['enabled'] ) ? 1 : 0;
			$existing['sort_order']        = isset( $posted_platform['sort_order'] ) ? absint( $posted_platform['sort_order'] ) : $existing['sort_order'];
			$existing['color']             = isset( $posted_platform['color'] ) ? ( sanitize_hex_color( $posted_platform['color'] ) ?: $existing['color'] ) : $existing['color'];
			$existing['light_color']       = isset( $posted_platform['light_color'] ) ? ( sanitize_hex_color( $posted_platform['light_color'] ) ?: $existing['light_color'] ) : $existing['light_color'];
			$platforms[ $slug ]            = $this->normalize_platform_payload( $existing, $slug );
		}

		update_option( 'cpai_tsb_platforms', $platforms );
	}

	private function handle_add_platform() {
		$new_platform = isset( $_POST['new_platform'] ) && is_array( $_POST['new_platform'] ) ? wp_unslash( $_POST['new_platform'] ) : array();
		$platforms    = $this->get_platforms();

		$requested_slug = isset( $new_platform['slug'] ) ? sanitize_title( $new_platform['slug'] ) : '';
		if ( empty( $requested_slug ) ) {
			$requested_slug = isset( $new_platform['name_en'] ) ? sanitize_title( $new_platform['name_en'] ) : '';
		}

		$slug = $this->generate_unique_slug( $requested_slug, array_keys( $platforms ) );
		if ( empty( $slug ) ) {
			return;
		}

		$defaults = $this->build_platform_defaults(
			$slug,
			isset( $new_platform['name_en'] ) ? sanitize_text_field( $new_platform['name_en'] ) : ucfirst( $slug ),
			isset( $new_platform['color'] ) ? sanitize_hex_color( $new_platform['color'] ) : '#2563eb',
			sprintf( 'Analyze %s', ucfirst( $slug ) )
		);

		$defaults['name_ur']     = isset( $new_platform['name_ur'] ) ? sanitize_text_field( $new_platform['name_ur'] ) : '';
		$defaults['icon']        = isset( $new_platform['icon'] ) ? sanitize_text_field( $new_platform['icon'] ) : '';
		$defaults['enabled']     = isset( $new_platform['enabled'] ) ? 1 : 0;
		$defaults['sort_order']  = isset( $new_platform['sort_order'] ) ? absint( $new_platform['sort_order'] ) : ( count( $platforms ) + 1 ) * 10;
		$defaults['light_color'] = isset( $new_platform['light_color'] ) ? ( sanitize_hex_color( $new_platform['light_color'] ) ?: '#eff6ff' ) : '#eff6ff';

		$platforms[ $slug ] = $this->normalize_platform_payload( $defaults, $slug );

		update_option( 'cpai_tsb_platforms', $platforms );
	}

	private function get_platforms() {
		$stored = get_option( 'cpai_tsb_platforms', array() );

		if ( isset( $stored[0] ) && is_array( $stored[0] ) && isset( $stored[0]['id'] ) ) {
			$stored = $this->migrate_legacy_platforms( $stored );
			update_option( 'cpai_tsb_platforms', $stored );
		}

		$defaults = $this->get_default_platforms();

		foreach ( $defaults as $slug => $default_platform ) {
			if ( ! isset( $stored[ $slug ] ) ) {
				$stored[ $slug ] = $default_platform;
			}
		}

		foreach ( $stored as $slug => $platform ) {
			$stored[ $slug ] = $this->normalize_platform_payload( $platform, $slug );
		}

		uasort( $stored, array( $this, 'compare_platform_sort_order' ) );

		return $stored;
	}

	private function compare_platform_sort_order( $a, $b ) {
		$order_a = isset( $a['sort_order'] ) ? absint( $a['sort_order'] ) : 0;
		$order_b = isset( $b['sort_order'] ) ? absint( $b['sort_order'] ) : 0;

		if ( $order_a === $order_b ) {
			$name_a = isset( $a['name_en'] ) ? strtolower( $a['name_en'] ) : '';
			$name_b = isset( $b['name_en'] ) ? strtolower( $b['name_en'] ) : '';
			return strcmp( $name_a, $name_b );
		}

		return $order_a - $order_b;
	}

	private function normalize_platform_payload( $platform, $fallback_slug ) {
		$platform = is_array( $platform ) ? $platform : array();
		$slug     = isset( $platform['id'] ) ? sanitize_key( $platform['id'] ) : sanitize_key( $fallback_slug );
		$default  = $this->build_platform_defaults( $slug, isset( $platform['name_en'] ) ? $platform['name_en'] : ucfirst( $slug ), isset( $platform['color'] ) ? $platform['color'] : '#2563eb', isset( $platform['button_label'] ) ? $platform['button_label'] : sprintf( 'Analyze %s', ucfirst( $slug ) ) );
		$platform = wp_parse_args( $platform, $default );

		$platform['id']           = $slug;
		$platform['name_en']      = sanitize_text_field( $platform['name_en'] );
		$platform['name_ur']      = sanitize_text_field( $platform['name_ur'] );
		$platform['icon']         = sanitize_text_field( $platform['icon'] );
		$platform['enabled']      = ! empty( $platform['enabled'] ) ? 1 : 0;
		$platform['title']        = sanitize_text_field( $platform['title'] );
		$platform['description']  = sanitize_textarea_field( $platform['description'] );
		$platform['color']        = sanitize_hex_color( $platform['color'] ) ?: $default['color'];
		$platform['light_color']  = sanitize_hex_color( $platform['light_color'] ) ?: $default['light_color'];
		$platform['button_label'] = sanitize_text_field( $platform['button_label'] );
		$platform['sort_order']   = isset( $platform['sort_order'] ) ? absint( $platform['sort_order'] ) : 0;
		$platform['questions']    = $this->normalize_questions_for_runtime( isset( $platform['questions'] ) ? $platform['questions'] : array() );

		return $platform;
	}

	private function generate_unique_slug( $requested_slug, $existing_slugs ) {
		$base_slug = sanitize_key( $requested_slug );
		if ( empty( $base_slug ) ) {
			return '';
		}

		if ( ! in_array( $base_slug, $existing_slugs, true ) ) {
			return $base_slug;
		}

		$suffix = 2;
		while ( in_array( $base_slug . '-' . $suffix, $existing_slugs, true ) ) {
			++$suffix;
		}

		return $base_slug . '-' . $suffix;
	}

	private function get_settings() {
		$defaults = array(
			'default_language' => 'en',
			'show_branding'    => 1,
			'items_per_page'   => 10,
			'dashboard_title'  => __( 'Teacher Social Branding Dashboard', 'coachpro-ai-teacher-social-branding' ),
			'primary_color'    => '#2271b1',
		);

		return wp_parse_args( get_option( 'cpai_tsb_settings', array() ), $defaults );
	}

	private function get_default_platforms() {
		return array(
			'facebook'  => $this->build_platform_defaults( 'facebook', 'Facebook', '#1877F2', 'Analyze Facebook', '#e8f1ff', 10, 'fab fa-facebook-f' ),
			'youtube'   => $this->build_platform_defaults( 'youtube', 'YouTube', '#FF0000', 'Analyze YouTube', '#fff1f2', 20, 'fab fa-youtube' ),
			'instagram' => $this->build_platform_defaults( 'instagram', 'Instagram', '#C13584', 'Analyze Instagram', '#fff1f7', 30, 'fab fa-instagram' ),
			'tiktok'    => $this->build_platform_defaults( 'tiktok', 'TikTok', '#000000', 'Analyze TikTok', '#f3f4f6', 40, 'fab fa-tiktok' ),
		);
	}

	private function build_platform_defaults( $slug, $name, $color, $button_label, $light_color = '#eff6ff', $sort_order = 10, $icon = '' ) {
		return array(
			'id'           => $slug,
			'name_en'      => $name,
			'name_ur'      => '',
			'icon'         => $icon,
			'enabled'      => 1,
			'sort_order'   => absint( $sort_order ),
			'title'        => sprintf( __( '%s Optimization', 'coachpro-ai-teacher-social-branding' ), $name ),
			'description'  => sprintf( __( 'Manage your %s-specific checklist and guidance.', 'coachpro-ai-teacher-social-branding' ), $name ),
			'color'        => $color,
			'light_color'  => $light_color,
			'button_label' => $button_label,
			'questions'    => array( $this->get_empty_question( 1 ) ),
		);
	}

	private function get_empty_question( $position ) {
		return array(
			'id'             => 'q' . absint( $position ),
			'text_en'        => '',
			'text_ur'        => '',
			'compare_left_image_url'  => '',
			'compare_right_image_url' => '',
			'instruction_en' => array(
				'title' => '',
				'steps' => array(),
				'tips'  => array(),
				'tool'  => '',
			),
			'instruction_ur' => array(
				'title' => '',
				'steps' => array(),
				'tips'  => array(),
				'tool'  => '',
			),
		);
	}

	private function sanitize_lines( $value ) {
		if ( is_array( $value ) ) {
			$lines = $value;
		} else {
			$lines = preg_split( '/\r\n|\r|\n/', sanitize_textarea_field( $value ) );
		}

		$lines = array_map( 'sanitize_text_field', $lines );
		$lines = array_filter( array_map( 'trim', $lines ) );

		return array_values( $lines );
	}

	private function normalize_questions_for_runtime( $questions ) {
		if ( ! is_array( $questions ) ) {
			return array( $this->get_empty_question( 1 ) );
		}

		$normalized = array();
		foreach ( $questions as $index => $question ) {
			$normalized[] = $this->normalize_question_payload( $question, $index + 1 );
		}

		if ( empty( $normalized ) ) {
			$normalized[] = $this->get_empty_question( 1 );
		}

		return $normalized;
	}

	private function normalize_question_payload( $question, $position ) {
		$question       = is_array( $question ) ? $question : array();
		$default_id     = 'q' . absint( $position );
		$sanitized_id   = isset( $question['id'] ) ? sanitize_key( $question['id'] ) : '';
		$instruction_en = isset( $question['instruction_en'] ) && is_array( $question['instruction_en'] ) ? $question['instruction_en'] : array();
		$instruction_ur = isset( $question['instruction_ur'] ) && is_array( $question['instruction_ur'] ) ? $question['instruction_ur'] : array();

		return array(
			'id'             => ! empty( $sanitized_id ) ? $sanitized_id : $default_id,
			'text_en'        => isset( $question['text_en'] ) ? sanitize_text_field( $question['text_en'] ) : '',
			'text_ur'        => isset( $question['text_ur'] ) ? sanitize_text_field( $question['text_ur'] ) : '',
			'compare_left_image_url'  => isset( $question['compare_left_image_url'] ) ? esc_url_raw( $question['compare_left_image_url'] ) : '',
			'compare_right_image_url' => isset( $question['compare_right_image_url'] ) ? esc_url_raw( $question['compare_right_image_url'] ) : '',
			'instruction_en' => $this->normalize_instruction_payload( $instruction_en ),
			'instruction_ur' => $this->normalize_instruction_payload( $instruction_ur ),
		);
	}

	private function normalize_instruction_payload( $instruction ) {
		$instruction = is_array( $instruction ) ? $instruction : array();

		return array(
			'title' => isset( $instruction['title'] ) ? sanitize_text_field( $instruction['title'] ) : '',
			'steps' => $this->sanitize_lines( isset( $instruction['steps'] ) ? $instruction['steps'] : '' ),
			'tips'  => $this->sanitize_lines( isset( $instruction['tips'] ) ? $instruction['tips'] : '' ),
			'tool'  => isset( $instruction['tool'] ) ? wp_kses_post( $instruction['tool'] ) : '',
		);
	}

	private function migrate_legacy_platforms( $legacy_platforms ) {
		$migrated = array();

		foreach ( $legacy_platforms as $index => $legacy_platform ) {
			if ( empty( $legacy_platform['id'] ) ) {
				continue;
			}

			$slug              = sanitize_key( $legacy_platform['id'] );
			$migrated[ $slug ] = wp_parse_args(
				$legacy_platform,
				$this->build_platform_defaults(
					$slug,
					isset( $legacy_platform['name_en'] ) ? $legacy_platform['name_en'] : ucfirst( $slug ),
					isset( $legacy_platform['color'] ) ? $legacy_platform['color'] : '#2271b1',
					sprintf( 'Analyze %s', ucfirst( $slug ) ),
					'#eff6ff',
					( $index + 1 ) * 10
				)
			);
			$migrated[ $slug ]['enabled']      = 1;
			$migrated[ $slug ]['title']        = sprintf( __( '%s Optimization', 'coachpro-ai-teacher-social-branding' ), $migrated[ $slug ]['name_en'] );
			$migrated[ $slug ]['description']  = sprintf( __( 'Manage your %s-specific checklist and guidance.', 'coachpro-ai-teacher-social-branding' ), $migrated[ $slug ]['name_en'] );
			$migrated[ $slug ]['button_label'] = sprintf( 'Analyze %s', $migrated[ $slug ]['name_en'] );
		}

		return $migrated;
	}

	private function render_admin_page( $template, $data = array() ) {
		$message = isset( $_GET['cpai_tsb_message'] ) ? sanitize_key( wp_unslash( $_GET['cpai_tsb_message'] ) ) : '';
		if ( ! empty( $message ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( ucwords( str_replace( '_', ' ', $message ) ) ) . '</p></div>';
		}

		extract( $data, EXTR_SKIP );
		include plugin_dir_path( __FILE__ ) . 'partials/' . $template;
	}

	private function redirect_with_message( $page, $message ) {
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'             => $page,
					'cpai_tsb_message' => $message,
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}
}
