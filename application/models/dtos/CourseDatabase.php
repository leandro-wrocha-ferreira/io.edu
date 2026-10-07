<?php

namespace app\models\dtos;

use DateTime;

/**
 * Database DTO representing a raw course record.
 */
final class CourseDatabase
{
	public int $id;
	public int $category_id;
	public string $title;
	public string $slug;
	public ?string $short_description;
	public ?string $description;
	public ?string $image;
	public ?string $image_url;
	public string $status;
	public ?int $workload_in_hours;
	public int $duration_in_seconds;
	public ?string $objectives;
	public ?string $target_audience;
	public ?string $requirements;
	public string $access_period_type;
	public ?int $access_days;
	public int $certificate_enabled;
	public DateTime $created_at;
	public ?DateTime $updated_at;
	public ?DateTime $deleted_at;
	public ?string $category_name;

	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $data
	 */
	public function __construct(array $data)
	{
		$this->id = (int) $data['id'];
		$this->category_id = (int) $data['category_id'];
		$this->title = (string) $data['title'];
		$this->slug = (string) $data['slug'];
		$this->short_description = isset($data['short_description']) && $data['short_description'] !== '' ? (string) $data['short_description'] : null;
		$this->description = isset($data['description']) && $data['description'] !== '' ? (string) $data['description'] : null;
		$this->image = isset($data['image']) && $data['image'] !== '' ? (string) $data['image'] : null;
		$this->image_url = isset($data['image_url']) && $data['image_url'] !== '' ? (string) $data['image_url'] : null;
		$this->status = (string) ($data['status'] ?? 'draft');
		$this->workload_in_hours = isset($data['workload_in_hours']) && $data['workload_in_hours'] !== '' && $data['workload_in_hours'] !== null ? (int) $data['workload_in_hours'] : null;
		$this->duration_in_seconds = isset($data['duration_in_seconds']) ? (int) $data['duration_in_seconds'] : 0;
		$this->objectives = isset($data['objectives']) && $data['objectives'] !== '' ? (string) $data['objectives'] : null;
		$this->target_audience = isset($data['target_audience']) && $data['target_audience'] !== '' ? (string) $data['target_audience'] : null;
		$this->requirements = isset($data['requirements']) && $data['requirements'] !== '' ? (string) $data['requirements'] : null;
		$this->access_period_type = (string) ($data['access_period_type'] ?? 'limited_time');
		$this->access_days = isset($data['access_days']) && $data['access_days'] !== '' && $data['access_days'] !== null ? (int) $data['access_days'] : null;
		$this->certificate_enabled = !empty($data['certificate_enabled']) ? 1 : 0;
		$this->created_at = !empty($data['created_at']) ? new DateTime((string) $data['created_at']) : new DateTime();
		$this->updated_at = !empty($data['updated_at']) ? new DateTime((string) $data['updated_at']) : null;
		$this->deleted_at = !empty($data['deleted_at']) ? new DateTime((string) $data['deleted_at']) : null;
		$this->category_name = isset($data['category_name']) && $data['category_name'] !== '' ? (string) $data['category_name'] : null;
	}
}
