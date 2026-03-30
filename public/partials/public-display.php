<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provide a public-facing view for the plugin
 *
 * @link       https://coachpro.ai/
 * @since      1.0.0
 * @package    CoachPro_AI_Social_Branding
 * @subpackage CoachPro_AI_Social_Branding/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php $instance_id = wp_unique_id( 'cpai-tsb-' ); ?>
<div id="<?php echo esc_attr( $instance_id ); ?>" class="cpai-tsb-wrapper" data-cpai-instance="<?php echo esc_attr( $instance_id ); ?>">

    <!-- Static Title -->
    <div class="cpai-tsb-main-title">
        COACHPRO AI<br>Teacher's Social Branding System
    </div>

    <!-- Sticky Header Navigation -->
    <div class="cpai-tsb-sticky-header">
        <div class="cpai-tsb-platforms-nav">
            <!-- Platforms injected via JS -->
        </div>

        <div class="cpai-tsb-language-selector">
            <button class="cpai-lang-btn active" data-lang="ur">Ur</button>
            <button class="cpai-lang-btn" data-lang="en">En</button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="cpai-tsb-content">
        <!-- Platform specific content will be injected here -->
        <div class="cpai-tsb-loading">Loading Branding System...</div>
    </div>

</div>
