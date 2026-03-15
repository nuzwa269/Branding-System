<?php

class CPAI_TSB {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'CPAI_TSB_VERSION' ) ) {
			$this->version = CPAI_TSB_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'coachpro-ai-teacher-social-branding';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cpai-tsb-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cpai-tsb-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cpai-tsb-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cpai-tsb-public.php';

		$this->loader = new CPAI_TSB_Loader();

	}

	private function set_locale() {
		$plugin_i18n = new CPAI_TSB_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	private function define_admin_hooks() {
		$plugin_admin = new CPAI_TSB_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
		$this->loader->add_action( 'admin_post_cpai_tsb_save_data', $plugin_admin, 'save_data' );
	}

	private function define_public_hooks() {
		$plugin_public = new CPAI_TSB_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		// Register shortcode
		$this->loader->add_shortcode( 'coachpro_ai_teacher_social_branding', $plugin_public, 'render_shortcode' );
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
