<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Language Detection Hook
 *
 * Detects the user's preferred language from the HTTP_ACCEPT_LANGUAGE header.
 * Defaults to 'english' if the requested language is unsupported or translated files are missing.
 *
 * Hook registered in post_controller_constructor.
 */
class Language_check
{
    /**
     * Detect browser language and load appropriate language files.
     *
     * @return void
     */
    public function detect(): void
    {
        $CI =& get_instance();

        $idiom = 'english';
        $accept_language = $CI->input->server('HTTP_ACCEPT_LANGUAGE');

        if ($accept_language && strpos($accept_language, 'pt') !== false) {
            $candidate = 'portuguese-brazilian';
            $lang_dir = APPPATH . 'language/' . $candidate;
            if (is_dir($lang_dir)) {
                $idiom = $candidate;
            }
        }

        $CI->config->set_item('language', $idiom);
        $CI->lang->load('admin', $idiom);
    }
}
