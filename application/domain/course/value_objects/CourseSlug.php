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
