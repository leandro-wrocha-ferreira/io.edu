<?php

namespace app\models\dtos;

use DateTime;

/**
 * Database DTO representing a raw lesson_blocks record with joined child table columns.
 */
final class LessonBlockDatabase
{
	public int $id;
	public int $lesson_id;
	public string $type;
	public int $sort_order;
	public int $is_required;
	public DateTime $created_at;
	public ?DateTime $updated_at;
	public ?DateTime $deleted_at;

	// Child fields
	public ?string $provider = null;
	public ?string $url = null;
	public ?int $duration_in_seconds = null;
	public ?string $content = null;
	public ?int $min_reading_time_in_minutes = null;
	public ?string $filename = null;
	public ?string $filepath = null;
	public ?int $file_size = null;
	public ?string $file_type = null;

	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $data
	 */
	public function __construct(array $data)
	{
		$this->id = (int) $data['id'];
		$this->lesson_id = (int) $data['lesson_id'];
		$this->type = (string) $data['type'];
		$this->sort_order = isset($data['sort_order']) ? (int) $data['sort_order'] : 0;
		$this->is_required = isset($data['is_required']) ? (int) $data['is_required'] : 1;
		$this->created_at = !empty($data['created_at']) ? new DateTime((string) $data['created_at']) : new DateTime();
		$this->updated_at = !empty($data['updated_at']) ? new DateTime((string) $data['updated_at']) : null;
		$this->deleted_at = !empty($data['deleted_at']) ? new DateTime((string) $data['deleted_at']) : null;

		$this->provider = isset($data['provider']) ? (string) $data['provider'] : null;
		$this->url = isset($data['url']) ? (string) $data['url'] : null;
		$this->duration_in_seconds = isset($data['duration_in_seconds']) ? (int) $data['duration_in_seconds'] : null;
		$this->content = isset($data['content']) ? (string) $data['content'] : null;
		$this->min_reading_time_in_minutes = isset($data['min_reading_time_in_minutes'])
			? (int) $data['min_reading_time_in_minutes']
			: (isset($data['min_reading_time_in_seconds']) ? (int) ceil(((int)$data['min_reading_time_in_seconds']) / 60) : null);
		$this->filename = isset($data['filename']) ? (string) $data['filename'] : null;
		$this->filepath = isset($data['filepath']) ? (string) $data['filepath'] : null;
		$this->file_size = isset($data['file_size']) ? (int) $data['file_size'] : null;
		$this->file_type = isset($data['file_type']) ? (string) $data['file_type'] : null;
	}
}
