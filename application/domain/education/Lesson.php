<?php

namespace app\domain\education;

/**
 * Lesson Entity representing a lesson inside a section.
 */
class Lesson
{
	private ?int $id = null;
	private int $section_id;
	private ?int $video_provider_id = null;
	private string $title;
	private ?string $video_identifier = null;
	private int $duration_seconds = 0;
	private ?string $content = null;
	private int $sort_order = 0;

	/**
	 * Create a new Lesson.
	 *
	 * @param int $section_id Section ID
	 * @param string $title Lesson title
	 * @param int $sort_order Sort order
	 * @return self
	 */
	public static function create(int $section_id, string $title, int $sort_order = 0): self
	{
		$lesson = new self();
		$lesson->section_id = $section_id;
		$lesson->title = $title;
		$lesson->sort_order = $sort_order;
		return $lesson;
	}

	/**
	 * Hydrate a Lesson from a database row.
	 *
	 * @param array $row Database row
	 * @return self
	 */
	public static function from_database(array $row): self
	{
		$lesson = new self();
		$lesson->id = (int) $row['id'];
		$lesson->section_id = (int) $row['section_id'];
		$lesson->video_provider_id = isset($row['video_provider_id']) ? (int) $row['video_provider_id'] : null;
		$lesson->title = $row['title'];
		$lesson->video_identifier = $row['video_identifier'] ?? null;
		$lesson->duration_seconds = (int) $row['duration_seconds'];
		$lesson->content = $row['content'] ?? null;
		$lesson->sort_order = (int) $row['sort_order'];
		return $lesson;
	}

	/**
	 * Get lesson ID.
	 *
	 * @return int|null
	 */
	public function get_id(): ?int
	{
		return $this->id;
	}

	/**
	 * Set lesson ID.
	 *
	 * @param int $id
	 * @return self
	 */
	public function set_id(int $id): self
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Get section ID.
	 *
	 * @return int
	 */
	public function get_section_id(): int
	{
		return $this->section_id;
	}

	/**
	 * Set section ID.
	 *
	 * @param int $section_id
	 * @return self
	 */
	public function set_section_id(int $section_id): self
	{
		$this->section_id = $section_id;
		return $this;
	}

	/**
	 * Get video provider ID.
	 *
	 * @return int|null
	 */
	public function get_video_provider_id(): ?int
	{
		return $this->video_provider_id;
	}

	/**
	 * Get lesson title.
	 *
	 * @return string
	 */
	public function get_title(): string
	{
		return $this->title;
	}

	/**
	 * Set lesson title.
	 *
	 * @param string $title
	 * @return self
	 */
	public function set_title(string $title): self
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Get video identifier.
	 *
	 * @return string|null
	 */
	public function get_video_identifier(): ?string
	{
		return $this->video_identifier;
	}

	/**
	 * Get duration in seconds.
	 *
	 * @return int
	 */
	public function get_duration_seconds(): int
	{
		return $this->duration_seconds;
	}

	/**
	 * Get text content.
	 *
	 * @return string|null
	 */
	public function get_content(): ?string
	{
		return $this->content;
	}

	/**
	 * Set text content.
	 *
	 * @param string|null $content
	 * @return self
	 */
	public function set_content(?string $content): self
	{
		$this->content = $content;
		return $this;
	}

	/**
	 * Get sort order.
	 *
	 * @return int
	 */
	public function get_sort_order(): int
	{
		return $this->sort_order;
	}

	/**
	 * Set sort order.
	 *
	 * @param int $sort_order
	 * @return self
	 */
	public function set_sort_order(int $sort_order): self
	{
		$this->sort_order = $sort_order;
		return $this;
	}

	/**
	 * Set video metadata.
	 *
	 * @param int $provider_id
	 * @param string $identifier
	 * @param int $duration
	 * @return self
	 */
	public function set_video_metadata(int $provider_id, string $identifier, int $duration): self
	{
		$this->video_provider_id = $provider_id;
		$this->video_identifier = $identifier;
		$this->duration_seconds = $duration;
		return $this;
	}
}
