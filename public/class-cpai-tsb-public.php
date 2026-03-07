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
			$legacy_platforms = array();
			foreach ( $stored_platforms as $platform ) {
				if ( ! empty( $platform['enabled'] ) ) {
					$legacy_platforms[] = $this->normalize_platform_for_public( $platform );
				}
			}

			return $legacy_platforms;
		}

		$platforms = array();
		foreach ( $stored_platforms as $platform ) {
			if ( empty( $platform['enabled'] ) ) {
				continue;
			}
			$platforms[] = $this->normalize_platform_for_public( $platform );
		}

		return $platforms;
	}

	private function normalize_platform_for_public( $platform ) {
		$questions = isset( $platform['questions'] ) && is_array( $platform['questions'] ) ? $platform['questions'] : array();
		$normalized_questions = array();

		foreach ( $questions as $index => $question ) {
			$normalized_questions[] = $this->normalize_question_for_public( $question, $index + 1 );
		}

		$platform['questions'] = $normalized_questions;

		return $platform;
	}

	private function normalize_question_for_public( $question, $position ) {
		$question = is_array( $question ) ? $question : array();

		$instruction_en = isset( $question['instruction_en'] ) && is_array( $question['instruction_en'] ) ? $question['instruction_en'] : array();
		$instruction_ur = isset( $question['instruction_ur'] ) && is_array( $question['instruction_ur'] ) ? $question['instruction_ur'] : array();

		return array(
			'id'             => ! empty( $question['id'] ) ? sanitize_key( $question['id'] ) : 'q' . absint( $position ),
			'text_en'        => isset( $question['text_en'] ) ? (string) $question['text_en'] : '',
			'text_ur'        => isset( $question['text_ur'] ) ? (string) $question['text_ur'] : '',
			'instruction_en' => $this->normalize_instruction_for_public( $instruction_en ),
			'instruction_ur' => $this->normalize_instruction_for_public( $instruction_ur ),
		);
	}

	private function normalize_instruction_for_public( $instruction ) {
		$instruction = is_array( $instruction ) ? $instruction : array();

		return array(
			'title' => isset( $instruction['title'] ) ? (string) $instruction['title'] : '',
			'steps' => isset( $instruction['steps'] ) && is_array( $instruction['steps'] ) ? array_values( $instruction['steps'] ) : array(),
			'tips'  => isset( $instruction['tips'] ) && is_array( $instruction['tips'] ) ? array_values( $instruction['tips'] ) : array(),
			'tool'  => isset( $instruction['tool'] ) ? (string) $instruction['tool'] : '',
		);
	}

	public function render_shortcode( $atts ) {
		ob_start();
		include 'partials/public-display.php';
		return ob_get_clean();
	}

}
