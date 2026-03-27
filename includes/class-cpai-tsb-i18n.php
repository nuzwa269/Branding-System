<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define the internationalization functionality
 *
 * @link       https://coachpro.ai/
 * @since      1.0.0
 * @package    CoachPro_AI_Social_Branding
 * @subpackage CoachPro_AI_Social_Branding/includes
 */

class CPAI_TSB_i18n {

	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'coachpro-ai-teacher-social-branding',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
