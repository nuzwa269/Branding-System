<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    CoachPro_AI_Social_Branding
 * @subpackage CoachPro_AI_Social_Branding/admin
 * @author     CoachPro AI <info@coachpro.ai>
 */
class CPAI_TSB_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles() {
		// Enqueue admin styles here if needed.
		// For simplicity, we might use inline styles or existing WP styles.
		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cpai-tsb-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		// Enqueue admin scripts here if needed.
		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cpai-tsb-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function add_plugin_admin_menu() {
		add_menu_page(
			'COACHPRO AI Teacher Social Branding',
			'COACHPRO AI Teacher Social Branding',
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_admin_page' ),
			'dashicons-share',
			6
		);
	}

	public function display_plugin_admin_page() {
		include_once 'partials/admin-display.php';
	}

	public function save_data() {
		// Check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Verify nonce
		if ( ! isset( $_POST['cpai_tsb_nonce'] ) || ! wp_verify_nonce( $_POST['cpai_tsb_nonce'], 'cpai_tsb_save_data' ) ) {
			return;
		}

		// Process and save data
		if ( isset( $_POST['cpai_tsb_platforms'] ) ) {
			$platforms = $_POST['cpai_tsb_platforms'];
			// Sanitize and validate data here (simplified for this example)
			// You would iterate through $platforms and sanitize each field.
			update_option( 'cpai_tsb_platforms', $platforms );
		}

		// Redirect back to admin page
		wp_redirect( add_query_arg( array( 'page' => 'coachpro-ai-teacher-social-branding', 'message' => '1' ), admin_url( 'admin.php' ) ) );
		exit;
	}

}
