<?php

namespace ContentAssistantOpenAIProvider;

use OpenAI;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * class Settings
 *
 * @namespace ContentAssistantOpenAIProvider
 */
class Settings {

    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
        add_filter('content_assistant_settings_tabs', [$this, 'add_settings_tab']);
        add_action('content_assistant_settings_content', [$this, 'display_settings_tab']);
    }

    public function register_settings() {
        register_setting('content_assistant_openai_settings', 'openai_api_key');
        register_setting('content_assistant_openai_settings', 'openai_provider_enabled', [
            'type' => 'boolean',
            'sanitize_callback' => function($value) {
                return (int) !empty($value);
            }
        ]);
        register_setting('content_assistant_openai_settings', 'openai_model', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'gpt-4'
        ]);
    }

    public function get_available_models() {
        $api_key = get_option('openai_api_key');

        if (empty($api_key)) {
            return ['gpt-4', 'gpt-4-turbo', 'gpt-3.5-turbo']; // Default fallback options
        }

        try {
            $client = OpenAI::client($api_key);
            $response = $client->models()->list();

            $models = [];
            foreach ($response['data'] as $model) {
                $models[] = $model['id'];
            }

            return $models;
        } catch (\Exception $e) {
            return ['gpt-4', 'gpt-4-turbo', 'gpt-3.5-turbo']; // Default if API fails
        }
    }

    public function add_settings_tab($tabs) {
        $tabs['openai_provider'] = 'OpenAI Provider';
        return $tabs;
    }

    public function display_settings_tab($active_tab) {
        if ($active_tab !== 'openai_provider') {
            return;
        }
        $selected_model = esc_attr(get_option('openai_model', 'gpt-4'));
        $models = $this->get_available_models();
        ?>
        <div class="wrap">
            <h2>OpenAI Provider Settings</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('content_assistant_openai_settings');
                do_settings_sections('content_assistant_openai_settings');
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="openai_provider_enabled">Enable OpenAI Provider</label></th>
                        <td>
                            <input type="checkbox" name="openai_provider_enabled" id="openai_provider_enabled"
                                   value="1" <?php checked(1, get_option('openai_provider_enabled', 0)); ?>>
                            <label for="openai_provider_enabled">Enable OpenAI Provider</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="openai_api_key">OpenAI API Key</label></th>
                        <td>
                            <input type="password" name="openai_api_key" id="openai_api_key"
                                   value="<?php echo esc_attr(get_option('openai_api_key')); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr class="openai-api-settings">
                        <th scope="row"><label for="openai_model">ChatGPT Model</label></th>
                        <td>
                            <select name="openai_model" id="openai_model">
                                <?php foreach ($models as $model): ?>
                                    <option value="<?php echo esc_attr($model); ?>" <?php selected($selected_model, $model); ?>>
                                        <?php echo esc_html($model); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">Select which OpenAI model to use for content generation.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
