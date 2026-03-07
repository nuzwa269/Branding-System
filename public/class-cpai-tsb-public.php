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
		$this->version     = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cpai-tsb-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4' );
		wp_enqueue_style( 'jameel-noori-nastaleeq', 'https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap', array(), null );
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

		wp_localize_script(
			$this->plugin_name,
			'cpai_tsb_data',
			array(
				'platforms' => $platforms,
				'strings'   => array(
					'title'      => $settings['dashboard_title'],
					'completed'  => 'Questions Completed',
					'next_phase' => 'اگلے مرحلے پر جائیں',
					'finish'     => 'Finish',
					'yes'        => 'Yes',
					'no'         => 'No',
					'great_en'   => 'Great! You can move to the next question.',
					'great_ur'   => 'بہت اچھا! اب اگلے سوال کی طرف بڑھیں۔',
				),
			)
		);
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

		$stored_platforms = wp_parse_args( $stored_platforms, $this->get_default_platforms() );
		$platforms        = array();
		foreach ( $stored_platforms as $platform ) {
			$normalized = $this->normalize_platform_for_public( $platform );
			if ( empty( $normalized['enabled'] ) ) {
				continue;
			}
			$platforms[] = $normalized;
		}

		usort(
			$platforms,
			function( $a, $b ) {
				$order_a = isset( $a['sort_order'] ) ? absint( $a['sort_order'] ) : 0;
				$order_b = isset( $b['sort_order'] ) ? absint( $b['sort_order'] ) : 0;
				return $order_a - $order_b;
			}
		);

		return $platforms;
	}

	private function normalize_platform_for_public( $platform ) {
		$platform  = is_array( $platform ) ? $platform : array();
		$slug      = isset( $platform['id'] ) ? sanitize_key( $platform['id'] ) : '';
		$fallbacks = $this->build_platform_defaults( $slug, isset( $platform['name_en'] ) ? $platform['name_en'] : ucfirst( $slug ), isset( $platform['color'] ) ? $platform['color'] : '#2563eb', isset( $platform['button_label'] ) ? $platform['button_label'] : sprintf( 'Analyze %s', ucfirst( $slug ) ) );
		$platform  = wp_parse_args( $platform, $fallbacks );

		$questions            = isset( $platform['questions'] ) && is_array( $platform['questions'] ) ? $platform['questions'] : array();
		$normalized_questions = array();

		foreach ( $questions as $index => $question ) {
			$normalized_questions[] = $this->normalize_question_for_public( $question, $index + 1 );
		}

		if ( empty( $normalized_questions ) ) {
			$normalized_questions[] = $this->normalize_question_for_public( array(), 1 );
		}

		$platform['id']          = $slug;
		$platform['name_en']     = isset( $platform['name_en'] ) ? (string) $platform['name_en'] : ucfirst( $slug );
		$platform['name_ur']     = isset( $platform['name_ur'] ) ? (string) $platform['name_ur'] : '';
		$platform['icon']        = isset( $platform['icon'] ) ? (string) $platform['icon'] : '';
		$platform['enabled']     = ! empty( $platform['enabled'] ) ? 1 : 0;
		$platform['color']       = sanitize_hex_color( $platform['color'] ) ?: '#2563eb';
		$platform['light_color'] = sanitize_hex_color( $platform['light_color'] ) ?: '#eff6ff';
		$platform['sort_order']  = isset( $platform['sort_order'] ) ? absint( $platform['sort_order'] ) : 0;
		$platform['questions']   = $normalized_questions;

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
			'title'        => $name . ' Optimization',
			'description'  => 'Manage your ' . $name . '-specific checklist and guidance.',
			'color'        => $color,
			'light_color'  => $light_color,
			'button_label' => $button_label,
			'questions'    => array(
				array(
					'id'             => 'q1',
					'text_en'        => '',
					'text_ur'        => '',
					'instruction_en' => array( 'title' => '', 'steps' => array(), 'tips' => array(), 'tool' => '' ),
					'instruction_ur' => array( 'title' => '', 'steps' => array(), 'tips' => array(), 'tool' => '' ),
				),
			),
		);
	}

	public function render_shortcode( $atts ) {
		ob_start();
		include 'partials/public-display.php';
		return ob_get_clean();
	}
}
