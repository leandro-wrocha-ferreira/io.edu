<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Language Detection Hook.
 *
 * Detects the user's preferred language/locale based on session preferences
 * and the HTTP_ACCEPT_LANGUAGE header.
 *
 * Defaults to 'english' (EN). If the user is from Brazil ('pt-BR' or 'pt'),
 * resolves to 'portuguese-brazilian' (PT-BR). For any other country, resolves to 'english'.
 *
 * Hook registered in post_controller_constructor.
 */
class Language_check
{
	/**
	 * Detect locale and load appropriate language files.
	 *
	 * @return void
	 */
	public function detect(): void
	{
		$instance =& get_instance();

		$idiom = 'english';

		// 1. Check user session preference if available
		$session_language = null;
		if (isset($instance->session)) {
			$session_language = $instance->session->userdata('language')
				?? $instance->session->userdata('locale')
				?? $instance->session->userdata('country');
		}

		if (!empty($session_language)) {
			$normalized = strtolower(trim((string) $session_language));
			if (in_array($normalized, ['pt', 'pt-br', 'br', 'brazil', 'brasil', 'portuguese-brazilian'], true)) {
				$idiom = 'portuguese-brazilian';
			} else {
				$idiom = 'english';
			}
		} else {
			// 2. Fall back to HTTP_ACCEPT_LANGUAGE header inspection
			$accept_language = $instance->input->server('HTTP_ACCEPT_LANGUAGE');
			if (!empty($accept_language)) {
				if (stripos($accept_language, 'pt-BR') !== false || stripos($accept_language, 'pt') !== false) {
					$idiom = 'portuguese-brazilian';
				} else {
					$idiom = 'english';
				}
			}
		}

		// Ensure the language directory exists before setting
		$language_directory = APPPATH . 'language/' . $idiom;
		if (!is_dir($language_directory)) {
			$idiom = 'english';
		}

		$instance->config->set_item('language', $idiom);
		$instance->lang->load('admin', $idiom);
		$instance->lang->load('exceptions', $idiom);
	}
}
