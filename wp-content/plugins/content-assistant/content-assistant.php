<?php
/**
 * Plugin Name:       Content Assistant
 * Plugin URI:        https://yaconiello.com
 * Description:       An AI powered content assistant.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Francis Yaconiello
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       content-assistant
 *
 * @package ContentAssistant
 */

namespace ContentAssistant;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Load Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Initialize settings page.
new Settings();

/**
 * Init hook
 */
function init() { }
add_action( 'init', __NAMESPACE__ . '\\init' );

/**
 * Enqueues the block's assets for the editor.
 */
function enqueue_block_editor_assets() {
	$content_assistant_file = plugin_dir_path( __FILE__ ) . 'build/content-assistant.asset.php';

	if ( file_exists( $content_assistant_file ) ) {
		$assets = include $content_assistant_file;
		wp_enqueue_script(
			'content-assistant',
			plugin_dir_url( __FILE__ ) . 'build/content-assistant.js',
			$assets['dependencies'],
			$assets['version'],
			true
		);
	}
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_block_editor_assets' );

/**
 * This filter is used to generate content based on the provided data.
 *
 * @param $content
 * @param $data
 *
 * @return string $value
 */
function content_assistant_generate_callback($content, $data) {
	$urls = isset($data['urls']) && is_array($data['urls']) ? implode(', ', $data['urls']) : 'No URLs provided';
	$prompt = isset($data['prompt']) ? sanitize_text_field($data['prompt']) : 'No prompt provided';

	$content .= "\n\nCrawlable URLs: $urls\n\n"
		. "Prompt: $prompt\n\n"
		. "Generated response: Install and configure a Content Assistant Provider plugin to get generated responses.";

	return $content;
}
add_filter('content_assistant_generate', __NAMESPACE__ . '\\content_assistant_generate_callback', 1, 2);

/**
 * Register REST API route with permission check
 */
function rest_api_init() {
	// Register REST Controller's routes.
	$controller = new ContentAssistantRestController();
	$controller->register_routes();
}
add_action('rest_api_init', __NAMESPACE__ . '\\rest_api_init');


