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

	/**
	 * Supported platform slugs.
	 *
	 * @var string[]
	 */
	private $platform_slugs = array( 'facebook', 'youtube', 'instagram', 'tiktok' );

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
		// Reserved for admin scripts.
	}

	public function add_plugin_admin_menu() {
		$capability = 'manage_options';

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

		foreach ( $this->platform_slugs as $platform_slug ) {
			$platform_title = ucfirst( $platform_slug );
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
				'settings' => $this->get_settings(),
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
			default:
				$this->redirect_with_message( $this->plugin_name, 'invalid_action' );
				break;
		}
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
		$platform['color']        = isset( $posted_platform['color'] ) ? sanitize_hex_color( $posted_platform['color'] ) : $platform['color'];
		$platform['button_label'] = isset( $posted_platform['button_label'] ) ? sanitize_text_field( $posted_platform['button_label'] ) : $platform['button_label'];

		$questions = isset( $posted_platform['questions'] ) && is_array( $posted_platform['questions'] ) ? $posted_platform['questions'] : array();
		$platform['questions'] = array();

		foreach ( $questions as $question ) {
			if ( empty( $question['text_en'] ) && empty( $question['text_ur'] ) ) {
				continue;
			}

			$platform['questions'][] = array(
				'id'             => isset( $question['id'] ) ? sanitize_key( $question['id'] ) : uniqid( 'q', false ),
				'text_en'        => isset( $question['text_en'] ) ? sanitize_text_field( $question['text_en'] ) : '',
				'text_ur'        => isset( $question['text_ur'] ) ? sanitize_text_field( $question['text_ur'] ) : '',
				'instruction_en' => array(
					'title' => isset( $question['instruction_en']['title'] ) ? sanitize_text_field( $question['instruction_en']['title'] ) : '',
					'steps' => $this->sanitize_lines( isset( $question['instruction_en']['steps'] ) ? $question['instruction_en']['steps'] : '' ),
					'tips'  => $this->sanitize_lines( isset( $question['instruction_en']['tips'] ) ? $question['instruction_en']['tips'] : '' ),
					'tool'  => isset( $question['instruction_en']['tool'] ) ? wp_kses_post( $question['instruction_en']['tool'] ) : '',
				),
				'instruction_ur' => array(
					'title' => isset( $question['instruction_ur']['title'] ) ? sanitize_text_field( $question['instruction_ur']['title'] ) : '',
					'steps' => $this->sanitize_lines( isset( $question['instruction_ur']['steps'] ) ? $question['instruction_ur']['steps'] : '' ),
					'tips'  => $this->sanitize_lines( isset( $question['instruction_ur']['tips'] ) ? $question['instruction_ur']['tips'] : '' ),
					'tool'  => isset( $question['instruction_ur']['tool'] ) ? wp_kses_post( $question['instruction_ur']['tool'] ) : '',
				),
			);
		}

		$platforms[ $platform_slug ] = $platform;
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

		return $stored;
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
			'facebook'  => $this->build_platform_defaults( 'facebook', 'Facebook', '#1877F2', 'Analyze Facebook' ),
			'youtube'   => $this->build_platform_defaults( 'youtube', 'YouTube', '#FF0000', 'Analyze YouTube' ),
			'instagram' => $this->build_platform_defaults( 'instagram', 'Instagram', '#C13584', 'Analyze Instagram' ),
			'tiktok'    => $this->build_platform_defaults( 'tiktok', 'TikTok', '#000000', 'Analyze TikTok' ),
		);
	}

	private function build_platform_defaults( $slug, $name, $color, $button_label ) {
		return array(
			'id'           => $slug,
			'name_en'      => $name,
			'name_ur'      => '',
			'icon'         => '',
			'enabled'      => 1,
			'title'        => sprintf( __( '%s Optimization', 'coachpro-ai-teacher-social-branding' ), $name ),
			'description'  => sprintf( __( 'Manage your %s-specific checklist and guidance.', 'coachpro-ai-teacher-social-branding' ), $name ),
			'color'        => $color,
			'button_label' => $button_label,
			'questions'    => array( $this->get_empty_question( 1 ) ),
		);
	}

	private function get_empty_question( $position ) {
		return array(
			'id'             => 'q' . absint( $position ),
			'text_en'        => '',
			'text_ur'        => '',
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
		$lines = preg_split( '/\r\n|\r|\n/', sanitize_textarea_field( $value ) );
		$lines = array_filter( array_map( 'trim', $lines ) );
		return array_values( $lines );
	}

	private function migrate_legacy_platforms( $legacy_platforms ) {
		$migrated = array();

		foreach ( $legacy_platforms as $legacy_platform ) {
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
					sprintf( 'Analyze %s', ucfirst( $slug ) )
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
