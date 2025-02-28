<?php

namespace ContentAssistant;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Settings {
	public function __construct() {
		add_action('admin_menu', [$this, 'add_settings_page']);
		add_action('content_assistant_settings_content', [$this, 'content_assistant_settings_content']);
	}

	public function add_settings_page() {
		add_options_page(
			'Content Assistant Settings',
			'Content Assistant',
			'manage_options',
			'content-assistant-settings',
			[$this, 'render_settings_page'],
			'dashicons-paperclip'
		);
	}

	public function render_settings_page() {
		$tabs = apply_filters('content_assistant_settings_tabs', [
			'general' => 'General Settings'
		]);
		$active_tab = isset($_GET['tab']) && array_key_exists($_GET['tab'], $tabs) ? $_GET['tab'] : 'general';

		echo '<div class="wrap"><h1>Content Assistant Settings</h1>';
		echo '<h2 class="nav-tab-wrapper">';
		foreach ($tabs as $tab_key => $tab_label) {
			$class = ($active_tab === $tab_key) ? 'nav-tab-active' : '';
			echo "<a href='?page=content-assistant-settings&tab=$tab_key' class='nav-tab $class'>$tab_label</a>";
		}
		echo '</h2>';

		do_action('content_assistant_settings_content', $active_tab);
		echo '</div>';
	}

	public function content_assistant_settings_content($active_tab) {
		if ($active_tab !== 'general') {
			return;
		}

		echo '<p>Configure a content provider in order to get AI-generated content for your posts.</p>';
	}
}
