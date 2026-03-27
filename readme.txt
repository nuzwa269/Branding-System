=== COACHPRO AI – Teacher's Social Branding System ===
Contributors: coachproai
Tags: education, social media, branding, teachers, bilingual
Requires at least: 6.4
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Interactive bilingual (English + Urdu) social-branding guidance for teachers with customizable platform checklists, question flows, and actionable improvement prompts.

== Description ==

COACHPRO AI – Teacher's Social Branding System helps educators evaluate and improve their social media branding using structured, platform-based question journeys.

Key capabilities:

* Bilingual interface and content support (English + Urdu).
* Platform-specific question sets (Facebook, YouTube, Instagram, TikTok by default).
* Compare-style improvement cards with actionable steps and tips.
* Admin controls for platform management, question editing, and settings.
* Import/export tools for checklist data (CSV and JSON).
* Shortcode for frontend rendering: `[coachpro_ai_teacher_social_branding]`.

This plugin stores its own settings and question data in WordPress options and does not require external accounts.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate the plugin from the **Plugins** menu in WordPress.
3. Open **Social Branding** in wp-admin to configure platforms/questions.
4. Add shortcode `[coachpro_ai_teacher_social_branding]` to any page/post.

== Frequently Asked Questions ==

= Does this plugin support Urdu content? =
Yes. Both admin-managed content and frontend interface labels support Urdu alongside English.

= Will my data be removed when I deactivate the plugin? =
No. Deactivation keeps your saved platforms/settings.

= Can I remove plugin data on uninstall? =
Yes. Add `define( 'CPAI_TSB_REMOVE_DATA_ON_UNINSTALL', true );` in `wp-config.php` before uninstalling.

== Changelog ==

= 1.0.0 =
* Initial stable release.
* Added WordPress.org-ready metadata/readme.
* Improved hardening for direct file access.
* Removed frontend third-party CDN requests to align with wordpress.org review expectations.
* Added uninstall routine with opt-in data cleanup.
