<?php

namespace app\models\dtos;

use DateTime;

/**
 * Database DTO representing a raw category record.
 */
final class CategoryDatabase
{
	public int $id;
	public string $name;
	public string $slug;
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
		$this->name = (string) $data['name'];
		$this->slug = (string) $data['slug'];
		$this->status = (string) ($data['status'] ?? 'active');
		$this->created_at = !empty($data['created_at']) ? new DateTime((string) $data['created_at']) : new DateTime();
		$this->updated_at = !empty($data['updated_at']) ? new DateTime((string) $data['updated_at']) : null;
		$this->deleted_at = !empty($data['deleted_at']) ? new DateTime((string) $data['deleted_at']) : null;
	}
}
