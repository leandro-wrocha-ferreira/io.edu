<?php

namespace app\models\dtos;

use DateTime;

/**
 * Database DTO representing a raw course_lessons record.
 */
final class CourseLessonDatabase
{
	public int $id;
	public int $module_id;
	public string $title;
	public ?string $description;
	public int $sort_order;
	public string $status;
	public DateTime $created_at;
	public ?DateTime $updated_at;
	public ?DateTime $deleted_at;

	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $data
	 */
	public function __construct(array $data)
	{
		$this->id = (int) $data['id'];
		$this->module_id = (int) $data['module_id'];
		$this->title = (string) $data['title'];
		$this->description = isset($data['description']) && $data['description'] !== '' ? (string) $data['description'] : null;
		$this->sort_order = isset($data['sort_order']) ? (int) $data['sort_order'] : 0;
		$this->status = (string) ($data['status'] ?? 'draft');
		$this->created_at = !empty($data['created_at']) ? new DateTime((string) $data['created_at']) : new DateTime();
		$this->updated_at = !empty($data['updated_at']) ? new DateTime((string) $data['updated_at']) : null;
		$this->deleted_at = !empty($data['deleted_at']) ? new DateTime((string) $data['deleted_at']) : null;
	}
}
