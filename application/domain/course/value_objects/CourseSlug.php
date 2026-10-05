<?php

namespace app\domain\course\value_objects;

use InvalidArgumentException;

/**
 * Value Object representing a course URL slug.
 *
 * Immutable by design.
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
	 * @throws InvalidArgumentException If slug format is invalid
	 */
	public function __construct(string $slug)
	{
		$trimmed = trim(strtolower($slug));
		if ($trimmed === '') {
			throw new InvalidArgumentException("Slug cannot be empty");
		}

		if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $trimmed)) {
			throw new InvalidArgumentException("Invalid slug format: {$slug}");
		}

		$this->value = $trimmed;
	}

	/**
	 * Create a slug from a raw title string.
	 *
	 * @param string $title
	 * @return self
	 */
	public static function from_title(string $title): self
	{
		$clean = mb_strtolower(trim($title), 'UTF-8');
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
		$clean = trim((string) $clean, '-');

		if ($clean === '') {
			$clean = 'curso-' . time();
		}

		return new self($clean);
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
