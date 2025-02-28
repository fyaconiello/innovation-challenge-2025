<?php

namespace ContentAssistant;

use WP_REST_Controller;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class ContentAssistantRestController
 *
 * Handles REST API requests for content assistance.
 */
class ContentAssistantRestController extends WP_REST_Controller {
	public function __construct() {
		$this->namespace = 'content-assistant/v1';
		$this->rest_base = 'generate';
	}

	public function register_routes() {
		register_rest_route($this->namespace, '/' . $this->rest_base, [
			[
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => [$this, 'generate_content'],
				'permission_callback' => [$this, 'permissions_check'],
			],
		]);
	}

	public function generate_content(WP_REST_Request $request) {
		$data = $request->get_json_params();

		if (!isset($data['urls']) || !isset($data['prompt'])) {
			return new WP_REST_Response(['error' => 'Missing data'], 400);
		}

		error_log("AJAX Triggered: " . print_r($data, true));

		$generated_content = apply_filters('content_assistant_generate', '', $data);

		error_log("Generated Content: " . $generated_content);

		return new WP_REST_Response(['generated_copy' => $generated_content], 200);
	}

	public function permissions_check() {
		if (!is_user_logged_in()) {
			return new WP_Error('rest_forbidden', __('You must be logged in.'), ['status' => 401]);
		}

		if (!current_user_can('edit_posts')) {
			return new WP_Error('rest_forbidden', __('You do not have permission to use this endpoint.'), ['status' => 403]);
		}

		return true;
	}
}
