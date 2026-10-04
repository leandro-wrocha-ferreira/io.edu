<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Branding & White-Label Helper — io.edu LMS
 *
 * Provides centralized tenant branding access, dynamic logo generation,
 * institution metadata, and CSS token overrides without view coupling.
 */

if (!function_exists('html_escape'))
{
	/**
	 * Fallback HTML escape function if CodeIgniter core is not fully loaded.
	 *
	 * @param mixed $var String or array to escape
	 * @param bool $double_encode Whether to encode existing HTML entities
	 * @return mixed Escaped string or array
	 */
	function html_escape($var, bool $double_encode = true)
	{
		if (is_array($var)) {
			return array_map('html_escape', $var);
		}
		return htmlspecialchars((string) ($var ?? ''), ENT_QUOTES, 'UTF-8', $double_encode);
	}
}

if (!function_exists('get_branding_data'))
{
	/**
	 * Retrieve branding settings for the active tenant.
	 *
	 * Falls back gracefully to the LMS Default Theme (Deep Indigo).
	 *
	 * @param array|null $custom_override Optional explicit override for testing
	 * @return array Branding parameters
	 */
	function get_branding_data(?array $custom_override = null): array
	{
		$custom = [];
		if ($custom_override !== null) {
			$custom = $custom_override;
		} elseif (function_exists('get_instance')) {
			$CI =& get_instance();
			if ($CI && isset($CI->config)) {
				$custom = $CI->config->item('branding') ?: [];
			}
		}

		// Default LMS Theme (Deep Indigo #4f46e5)
		$defaults = [
			'institution_name' => 'io.edu',
			'institution_short' => 'IOedu',
			'institution_tag' => 'Plataforma de Educação',
			'logo_url' => null, // when null, uses dynamic SVG logo
			'favicon_url' => null,
			'primary_color' => '#4f46e5',
			'primary_hover' => '#4338ca',
			'primary_active' => '#3730a3',
			'primary_ghost' => 'rgba(79, 70, 229, 0.08)',
			'primary_gradient' => 'linear-gradient(135deg, #4f46e5 0%, #312e81 100%)',
			'accent_color' => '#06b6d4',
			'radius_base' => '10px',
		];

		return array_merge($defaults, $custom);
	}
}

if (!function_exists('get_institution_name'))
{
	/**
	 * Get the institution display name.
	 *
	 * @param array|null $custom_branding Optional explicit override
	 * @return string
	 */
	function get_institution_name(?array $custom_branding = null): string
	{
		$branding = $custom_branding !== null ? get_branding_data($custom_branding) : get_branding_data();
		return (string) ($branding['institution_name'] ?? 'io.edu');
	}
}

if (!function_exists('get_institution_short'))
{
	/**
	 * Get the institution short name / acronym.
	 *
	 * @param array|null $custom_branding Optional explicit override
	 * @return string
	 */
	function get_institution_short(?array $custom_branding = null): string
	{
		$branding = $custom_branding !== null ? get_branding_data($custom_branding) : get_branding_data();
		return (string) ($branding['institution_short'] ?? 'IOedu');
	}
}

if (!function_exists('get_institution_logo'))
{
	/**
	 * Render the institution logo (image or branded SVG badge).
	 *
	 * @param string $class Additional CSS classes
	 * @param array|null $custom_branding Optional explicit override
	 * @return string HTML logo element
	 */
	function get_institution_logo(string $class = '', ?array $custom_branding = null): string
	{
		$branding = $custom_branding !== null ? get_branding_data($custom_branding) : get_branding_data();

		if (!empty($branding['logo_url'])) {
			return sprintf(
				'<img src="%s" alt="%s" class="%s" height="36">',
				html_escape($branding['logo_url']),
				html_escape($branding['institution_name']),
				html_escape($class)
			);
		}

		// Clean modern SVG Logo Icon + Typography
		return sprintf(
			'<span class="d-inline-flex align-items-center gap-2 text-decoration-none %s">
				<span class="edu-brand-icon" aria-hidden="true">
					<i class="bi bi-mortarboard-fill"></i>
				</span>
				<span class="edu-brand-text">io<span>.edu</span></span>
			</span>',
			html_escape($class)
		);
	}
}

if (!function_exists('render_branding_styles'))
{
	/**
	 * Render dynamic CSS token overrides in the HTML head.
	 *
	 * @param array|null $custom_branding Optional explicit override
	 * @return string HTML style block or empty string if default
	 */
	function render_branding_styles(?array $custom_branding = null): string
	{
		$branding = $custom_branding !== null ? get_branding_data($custom_branding) : get_branding_data();

		// If matching defaults, no extra style override is needed
		if ($branding['primary_color'] === '#4f46e5' && empty($branding['custom_override'])) {
			return '';
		}

		$css = sprintf(
			':root {
				--edu-primary: %s;
				--edu-primary-hover: %s;
				--edu-primary-active: %s;
				--edu-primary-ghost: %s;
				--edu-primary-gradient: %s;
				--edu-accent: %s;
			}',
			$branding['primary_color'],
			$branding['primary_hover'],
			$branding['primary_active'],
			$branding['primary_ghost'],
			$branding['primary_gradient'],
			$branding['accent_color']
		);

		return "<style id=\"edu-branding-override\">{$css}</style>\n";
	}
}
