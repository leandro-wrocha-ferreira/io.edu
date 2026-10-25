<?php

namespace app\domain\finance;

use app\domain\exceptions\ValidationException;
use DateTime;

/**
 * Domain entity representing an independent managerial cost center.
 */
final class CostCenter
{
	private ?int $id = null;
	private string $name;
	private ?string $description = null;
	private bool $is_active = true;
	private ?DateTime $created_at = null;
	private ?DateTime $updated_at = null;
	private ?DateTime $deleted_at = null;

	/**
	 * Create a CostCenter domain entity.
	 *
	 * @param string $name Cost center name
	 * @param string|null $description Optional description
	 * @param bool $is_active Active status
	 * @param int|null $id Cost center ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @return self
	 * @throws ValidationException When validation fails
	 */
	public static function create(
		string $name,
		?string $description = null,
		bool $is_active = true,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null
	): self
	{
		$clean_name = trim($name);
		if ($clean_name === '') {
			throw new ValidationException("Cost center name cannot be empty");
		}

		$cost_center = new self();
		$cost_center->id = $id;
		$cost_center->name = $clean_name;
		$cost_center->description = $description !== null && trim($description) !== '' ? trim($description) : null;
		$cost_center->is_active = $is_active;
		$cost_center->created_at = $created_at ?? new DateTime();
		$cost_center->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);
		$cost_center->deleted_at = $deleted_at;

		return $cost_center;
	}

	/**
	 * Update cost center attributes.
	 *
	 * @param string $name Cost center name
	 * @param string|null $description Optional description
	 * @param bool $is_active Active status
	 * @return void
	 * @throws ValidationException When validation fails
	 */
	public function update(string $name, ?string $description = null, bool $is_active = true): void
	{
		$clean_name = trim($name);
		if ($clean_name === '') {
			throw new ValidationException("Cost center name cannot be empty");
		}

		$this->name = $clean_name;
		$this->description = $description !== null && trim($description) !== '' ? trim($description) : null;
		$this->is_active = $is_active;
		$this->updated_at = new DateTime();
	}

	public function activate(): void
	{
		$this->is_active = true;
		$this->updated_at = new DateTime();
	}

	public function deactivate(): void
	{
		$this->is_active = false;
		$this->updated_at = new DateTime();
	}

	public function get_id(): ?int
	{
		return $this->id;
	}

	public function get_name(): string
	{
		return $this->name;
	}

	public function get_description(): ?string
	{
		return $this->description;
	}

	public function is_active(): bool
	{
		return $this->is_active;
	}

	public function get_created_at(): ?DateTime
	{
		return $this->created_at;
	}

	public function get_updated_at(): ?DateTime
	{
		return $this->updated_at;
	}

	public function get_deleted_at(): ?DateTime
	{
		return $this->deleted_at;
	}
}
