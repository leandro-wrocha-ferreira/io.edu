<?php

namespace app\factories;

/**
 * Factory for loading CI3 libraries.
 *
 * Provides a clean, decoupled way for controllers and application services
 * to load libraries without directly coupling to CI3's get_instance().
 */
class LibraryFactory
{
	/**
	 * Load and return a CI3 library instance.
	 *
	 * @param string $name Library name (e.g. 'storage_library')
	 * @param array<string, mixed> $params Optional constructor parameters
	 * @return object The loaded library instance
	 */
	public static function make(string $name, array $params = [])
	{
		$CI =& get_instance();
		$CI->load->library($name, $params);
		return $CI->{$name};
	}
}
