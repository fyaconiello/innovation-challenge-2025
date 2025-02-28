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

function content_assistant_generate($content, $data) {
    error_log("In: ContentAssistantOpenAIProvider\content_assistant_generate() : " . print_r([$content, $data], true));

    $is_enabled = get_option('openai_provider_enabled', 0);

    $urls = isset($data['urls']) && is_array($data['urls']) ? implode(', ', $data['urls']) : 'No URLs provided';
    $prompt = isset($data['prompt']) ? sanitize_text_field($data['prompt']) : 'No prompt provided';

    if ($is_enabled && !empty($prompt)) {
        $prompt = "\n\nCrawlable URLs: $urls\n\n"
            . "Prompt: $prompt\n\n";

        $openai = new OpenAIHandler();
        $content = $openai->generateResponse($prompt);
    }

    return $content;
}
add_action('content_assistant_generate', __NAMESPACE__ . '\\content_assistant_generate', 10, 2);

