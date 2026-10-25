<?php

namespace app\domain\course\value_objects;

use InvalidArgumentException;

/**
 * Value Object representing a course URL slug.
 *
 * Immutable by design. Normalizes text into safe URL-friendly kebab-case format.
 */
final class CourseSlug
{
	/**
	 * Slug value.
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * Constructor.
	 *
	 * @param string $slug
	 * @throws InvalidArgumentException If slug cannot be formed or is empty
	 */
	public function __construct(string $slug)
	{
		$trimmed = self::slugify($slug);
		if ($trimmed === '') {
			throw new InvalidArgumentException("Slug cannot be empty");
		}

		$this->value = $trimmed;
	}

	/**
	 * Convert arbitrary text into a safe lowercase kebab-case slug.
	 *
	 * @param string $text Raw input text
	 * @return string Safe slug string
	 */
	public static function slugify(string $text): string
	{
		$clean = mb_strtolower(trim($text), 'UTF-8');
		if (function_exists('transliterator_transliterate')) {
			$clean = \transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $clean);
		} elseif (function_exists('iconv')) {
			$converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $clean);
			if ($converted !== false && $converted !== '') {
				$clean = $converted;
			}
		}

		$clean = preg_replace('/[^a-z0-9\s-]/', '', (string) $clean);
		$clean = preg_replace('/[\s-]+/', '-', (string) $clean);
		return trim((string) $clean, '-');
	}

	/**
	 * Check if a slug string contains the '[id]-' or 'id-' placeholder prefix.
	 *
	 * @param string $slug
	 * @return bool
	 */
	public static function has_id_placeholder(string $slug): bool
	{
		return (bool) preg_match('/^(\[id\]-|id-)/i', trim($slug));
	}

	/**
	 * Extract clean base slug text by removing any leading '[id]-' or 'id-' placeholder.
	 *
	 * @param string $slug_or_title
	 * @return string
	 */
	public static function extract_base_slug(string $slug_or_title): string
	{
		$clean = trim($slug_or_title);
		$clean = preg_replace('/^(\[id\]-|id-)/i', '', $clean);
		return self::slugify($clean);
	}

	/**
	 * Format a course slug with its numeric course ID prefix: "{course_id}-{clean_slug}".
	 *
	 * Strips placeholder prefixes like '[id]-' or 'id-', and course ID prefix if already present,
	 * ensuring the returned slug is always "{course_id}-{clean_slug}".
	 *
	 * @param int $course_id Course ID
	 * @param string $slug_or_title Raw slug or title
	 * @return self
	 */
	public static function format_with_id(int $course_id, string $slug_or_title): self
	{
		$clean = trim($slug_or_title);
		$clean = preg_replace('/^(\[id\]-|id-)/i', '', $clean);
		$clean = preg_replace('/^' . preg_quote((string) $course_id, '/') . '-/i', '', $clean);
		$clean_slug = self::slugify($clean);

		if ($clean_slug === '') {
			$clean_slug = 'curso';
		}

		return new self($course_id . '-' . $clean_slug);
	}

	/**
	 * Create a CourseSlug from raw string or title.
	 *
	 * @param string $text
	 * @return self
	 */
	public static function from_string(string $text): self
	{
		return new self($text);
	}

	/**
	 * Get the slug value.
	 *
	 * @return string
	 */
	public function get_value(): string
	{
		return $this->value;
	}

	/**
	 * Return string representation.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->value;
	}

	/**
	 * Check equality with another CourseSlug.
	 *
	 * @param CourseSlug $other
	 * @return bool
	 */
	public function equals(CourseSlug $other): bool
	{
		return $this->value === (string) $other;
	}
}
