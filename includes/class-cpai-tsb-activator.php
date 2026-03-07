<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 * @package    CoachPro_AI_Social_Branding
 * @subpackage CoachPro_AI_Social_Branding/includes
 */
class CPAI_TSB_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// Initialize default content if not exists
		if ( false === get_option( 'cpai_tsb_platforms' ) ) {
			$default_platforms = array(
				array(
					'id' => 'facebook',
					'name_en' => 'Facebook',
					'name_ur' => 'فیس بک',
					'icon' => 'fab fa-facebook-f',
					'color' => '#1877F2',
					'questions' => self::generate_questions('Facebook')
				),
				array(
					'id' => 'youtube',
					'name_en' => 'YouTube',
					'name_ur' => 'یوٹیوب',
					'icon' => 'fab fa-youtube',
					'color' => '#FF0000',
					'questions' => self::generate_questions('YouTube')
				),
				array(
					'id' => 'instagram',
					'name_en' => 'Instagram',
					'name_ur' => 'انسٹاگرام',
					'icon' => 'fab fa-instagram',
					'color' => '#C13584',
					'questions' => self::generate_questions('Instagram')
				),
				array(
					'id' => 'tiktok',
					'name_en' => 'TikTok',
					'name_ur' => 'ٹک ٹاک',
					'icon' => 'fab fa-tiktok',
					'color' => '#000000',
					'questions' => self::generate_questions('TikTok')
				),
			);
			update_option( 'cpai_tsb_platforms', $default_platforms );
		}

	}

	private static function generate_questions($platform_name) {
		$questions = array();
		for ($i = 1; $i <= 10; $i++) {
			$questions[] = array(
				'id' => 'q' . $i,
				'text_en' => "Is your $platform_name profile picture professional and clear?",
				'text_ur' => "کیا آپ کی $platform_name پروفائل تصویر پیشہ ورانہ اور واضح ہے؟",
				'instruction_en' => array(
					'title' => 'Optimize Profile Picture',
					'steps' => array("Use a high-quality headshot.", "Ensure good lighting.", "Avoid distractions in the background."),
					'tips' => array("Professional Tip 1: Look directly at the camera.", "Professional Tip 2: Smile naturally."),
					'tool' => ''
				),
				'instruction_ur' => array(
					'title' => 'پروفائل تصویر کو بہتر بنائیں',
					'steps' => array("اعلیٰ معیار کی ہیڈ شاٹ استعمال کریں۔", "اچھی روشنی کو یقینی بنائیں۔", "پس منظر میں خلفشار سے بچیں۔"),
					'tips' => array("پیشہ ورانہ ٹپ 1: براہ راست کیمرے کی طرف دیکھیں۔", "پیشہ ورانہ ٹپ 2: قدرتی طور پر مسکرائیں۔"),
					'tool' => ''
				)
			);
		}
		return $questions;
	}

}
