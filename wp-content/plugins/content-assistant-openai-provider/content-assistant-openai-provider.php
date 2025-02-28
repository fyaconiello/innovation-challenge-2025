<?php
/**
 * Plugin Name:       Content Assistant - OpenAI Provider
 * Plugin URI:        https://yaconiello.com
 * Description:       Provides OpenAI backend to the content assistant plugin.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      8.2
 * Author:            Francis Yaconiello
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       content-assistant
 *
 * @package ContentAssistantOpenAIProvider
 */

namespace ContentAssistantOpenAIProvider;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Load Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

function admin_menu() {
    new Settings();
}
add_action('admin_menu', __NAMESPACE__ . '\\admin_menu');

function content_assistant_generate($prompt, $args) {
    $is_enabled = get_option('openai_provider_enabled', 0);

    if ($is_enabled) {
        $openai = new OpenAIHandler();
        return $openai->generateResponse($prompt, $args);
    }
    return '';
}
add_action('content_assistant_generate', __NAMESPACE__ . '\\content_assistant_generate', 10, 2);

