<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * administrative area. This file also includes all of the plugin dependencies.
 *
 * @link              https://coachpro.ai
 * @since             1.0.0
 * @package           CoachPro_AI_Social_Branding
 *
 * Plugin Name:       COACHPRO AI – Teacher's Social Branding System
 * Plugin URI:        https://coachpro.ai/
 * Description:       An interactive social media branding guidance system for teachers, supporting English and Urdu.
 * Version:           1.0.0
 * Author:            CoachPro AI
 * Author URI:        https://coachpro.ai/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       coachpro-ai-teacher-social-branding
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'CPAI_TSB_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 */
function activate_cpai_tsb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cpai-tsb-activator.php';
	CPAI_TSB_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_cpai_tsb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cpai-tsb-deactivator.php';
	CPAI_TSB_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cpai_tsb' );
register_deactivation_hook( __FILE__, 'deactivate_cpai_tsb' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cpai-tsb.php';

/**
 * Begins execution of the plugin.
 */
function run_cpai_tsb() {
	$plugin = new CPAI_TSB();
	$plugin->run();
}
run_cpai_tsb();
