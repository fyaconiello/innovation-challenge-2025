<?php

namespace ContentAssistantOpenAIProvider;

use Exception;
use OpenAI;

if (!defined('ABSPATH')) {
    exit;
}

class OpenAIHandler {
    private $client;

    public function __construct() {
        $api_key = get_option('openai_api_key');

        if (empty($api_key)) {
            throw new Exception("OpenAI API key is missing. Please configure it in the settings.");
        }

        $this->client = OpenAI::client($api_key);
    }

    public function generateResponse($prompt) {
        try {
            $response = $this->client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an AI assistant.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 500
            ]);

            return $response->choices[0]->message->content ?? "No response received.";
        }
        catch (Exception $e) {
            return "Error calling OpenAI: " . $e->getMessage();
        }
    }
}
