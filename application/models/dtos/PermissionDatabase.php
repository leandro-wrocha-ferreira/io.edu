<?php

namespace app\models\dtos;

use DateTime;

/**
 * Database DTO representing a raw permission record.
 */
final class PermissionDatabase
{
	public int $id;
	public string $name;
	public string $slug;
	public ?string $description;
	public DateTime $created_at;
	public ?DateTime $updated_at;

	/**
	 * Constructor.
	 *
	 * @param array{id: int|string, name: string, slug: string, description?: ?string, created_at?: ?string, updated_at?: ?string} $data
	 */
	public function __construct(array $data)
	{
		$this->id = (int) $data['id'];
		$this->name = (string) $data['name'];
		$this->slug = (string) $data['slug'];
		$this->description = !empty($data['description']) ? (string) $data['description'] : null;
		$this->created_at = !empty($data['created_at']) ? new DateTime($data['created_at']) : new DateTime();
		$this->updated_at = !empty($data['updated_at']) ? new DateTime($data['updated_at']) : null;
	}
}
