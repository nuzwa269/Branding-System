<?php

/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    CoachPro_AI_Social_Branding
 * @subpackage CoachPro_AI_Social_Branding/includes
 */
class CPAI_TSB_Activator {

	/**
	 * Runs on activation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activate() {
		if ( false === get_option( 'cpai_tsb_platforms' ) ) {
			update_option( 'cpai_tsb_platforms', self::default_platforms() );
		}

		if ( false === get_option( 'cpai_tsb_settings' ) ) {
			update_option(
				'cpai_tsb_settings',
				array(
					'default_language' => 'en',
					'show_branding'    => 1,
					'items_per_page'   => 10,
					'dashboard_title'  => 'Teacher Social Branding Dashboard',
					'primary_color'    => '#2271b1',
				)
			);
		}
	}

	/**
	 * Get default platform data.
	 *
	 * @return array
	 */
	private static function default_platforms() {
		return array(
			'facebook'  => self::build_platform_defaults( 'facebook', 'Facebook', '#1877F2', 'Analyze Facebook' ),
			'youtube'   => self::build_platform_defaults( 'youtube', 'YouTube', '#FF0000', 'Analyze YouTube' ),
			'instagram' => self::build_platform_defaults( 'instagram', 'Instagram', '#C13584', 'Analyze Instagram' ),
			'tiktok'    => self::build_platform_defaults( 'tiktok', 'TikTok', '#000000', 'Analyze TikTok' ),
		);
	}

	/**
	 * Build defaults for a single platform.
	 *
	 * @param string $id Platform id.
	 * @param string $name Platform name.
	 * @param string $color Platform color.
	 * @param string $button_label Button label.
	 *
	 * @return array
	 */
	private static function build_platform_defaults( $id, $name, $color, $button_label ) {
		return array(
			'id'           => $id,
			'name_en'      => $name,
			'name_ur'      => '',
			'icon'         => '',
			'enabled'      => 1,
			'title'        => $name . ' Optimization',
			'description'  => 'Manage your ' . $name . '-specific checklist and guidance.',
			'color'        => $color,
			'button_label' => $button_label,
			'questions'    => array( self::empty_question( 1 ) ),
		);
	}

	/**
	 * Empty question scaffold.
	 *
	 * @param int $index Question position.
	 *
	 * @return array
	 */
	private static function empty_question( $index ) {
		return array(
			'id'             => 'q' . absint( $index ),
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
}
