<?php
/**
 * Uninstall handler for COACHPRO AI – Teacher's Social Branding System.
 *
 * @package CoachPro_AI_Social_Branding
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! defined( 'CPAI_TSB_REMOVE_DATA_ON_UNINSTALL' ) || true !== CPAI_TSB_REMOVE_DATA_ON_UNINSTALL ) {
	return;
}

delete_option( 'cpai_tsb_platforms' );
delete_option( 'cpai_tsb_settings' );
