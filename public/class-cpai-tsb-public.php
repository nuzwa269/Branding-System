<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    CoachPro_AI_Social_Branding
 * @subpackage CoachPro_AI_Social_Branding/public
 * @author     CoachPro AI <info@coachpro.ai>
 */
class CPAI_TSB_Public {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cpai-tsb-public.css', array(), $this->version, 'all' );
		// Enqueue FontAwesome for icons
		wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4' );
		// Enqueue Urdu font
		wp_enqueue_style( 'jameel-noori-nastaleeq', 'https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap', array(), null );
		// Note: Jameel Noori isn't on Google Fonts directly usually, using Noto Nastaliq as fallback or assume custom font face in CSS.
		// But let's try to stick to standard available fonts or include the font file.
		// For this example, I'll rely on CSS @font-face or system fallback, but Noto Nastaliq is a good alternative if Jameel isn't hosted.
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cpai-tsb-public.js', array( 'jquery' ), $this->version, false );

		$platforms = $this->get_public_platforms();
		$settings  = wp_parse_args(
			get_option( 'cpai_tsb_settings', array() ),
			array(
				'dashboard_title' => "COACHPRO AI\nTeacher's Social Branding System",
			)
		);

		wp_localize_script( $this->plugin_name, 'cpai_tsb_data', array(
			'platforms' => $platforms,
			'strings' => array(
				'title' => $settings['dashboard_title'],
				'completed' => 'Questions Completed',
				'next_phase' => 'اگلے مرحلے پر جائیں', // Go to next phase
				'finish' => 'Finish',
				'yes' => 'Yes',
				'no' => 'No',
				'great_en' => 'Great! You can move to the next question.',
				'great_ur' => 'بہت اچھا! اب اگلے سوال کی طرف بڑھیں۔'
			)
		));
	}



	/**
	 * Get frontend-ready platform list.
	 *
	 * @return array
	 */
	private function get_public_platforms() {
		$stored_platforms = get_option( 'cpai_tsb_platforms', array() );

		if ( isset( $stored_platforms[0] ) ) {
			return $stored_platforms;
		}

		$platforms = array();
		foreach ( $stored_platforms as $platform ) {
			if ( empty( $platform['enabled'] ) ) {
				continue;
			}
			$platforms[] = $platform;
		}

		return $platforms;
	}

	public function render_shortcode( $atts ) {
		ob_start();
		include 'partials/public-display.php';
		return ob_get_clean();
	}

}
