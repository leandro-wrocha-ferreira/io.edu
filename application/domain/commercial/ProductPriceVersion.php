<?php

namespace app\domain\commercial;

use app\domain\commercial\value_objects\Price;
use app\domain\exceptions\ValidationException;
use DateTime;

/**
 * Domain entity representing an immutable historical snapshot of product commercial conditions upon a price change.
 */
final class ProductPriceVersion
{
	private ?int $id = null;
	private int $product_id;
	private int $version_number;
	private bool $is_free = false;
	private ?Price $previous_price = null;
	private ?Price $new_price = null;
	private string $currency;
	private array $snapshot;
	private ?string $change_reason = null;
	private ?int $created_by = null;
	private DateTime $created_at;

	/**
	 * Create a product price version domain entity.
	 *
	 * @param int $product_id Associated product ID
	 * @param int $version_number Sequential version number (1, 2, 3...)
	 * @param Price|null $previous_price Previous price (null for version 1)
	 * @param Price|null $new_price New defined price (null if free)
	 * @param array $snapshot Denormalized commercial snapshot
	 * @param string|null $change_reason Justification for price change
	 * @param int|null $created_by Authenticated user ID responsible
	 * @param bool $is_free Free product flag
	 * @param int|null $id Version record ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param string $currency Currency code
	 * @return self
	 * @throws ValidationException When invariants are violated
	 */
	public static function create(
		int $product_id,
		int $version_number,
		?Price $previous_price,
		?Price $new_price,
		array $snapshot,
		?string $change_reason = null,
		?int $created_by = null,
		bool $is_free = false,
		?int $id = null,
		?DateTime $created_at = null,
		string $currency = 'BRL'
	): self
	{
		if ($product_id <= 0) {
			throw new ValidationException("Product ID must be greater than 0");
		}

		if ($version_number <= 0) {
			throw new ValidationException("Version number must be greater than 0");
		}

		$version = new self();
		$version->id = $id;
		$version->product_id = $product_id;
		$version->version_number = $version_number;
		$version->is_free = $is_free;
		$version->previous_price = $previous_price;
		$version->new_price = $new_price;
		$version->currency = $currency;
		$version->snapshot = $snapshot;
		$version->change_reason = $change_reason;
		$version->created_by = $created_by;
		$version->created_at = $created_at ?? new DateTime();

		return $version;
	}

	public function get_id(): ?int
	{
		return $this->id;
	}

	public function get_product_id(): int
	{
		return $this->product_id;
	}

	public function get_version_number(): int
	{
		return $this->version_number;
	}

	public function is_free(): bool
	{
		return $this->is_free;
	}

	public function get_previous_price(): ?Price
	{
		return $this->previous_price;
	}

	public function get_new_price(): ?Price
	{
		return $this->new_price;
	}

	public function get_currency(): string
	{
		return $this->currency;
	}

	public function get_snapshot(): array
	{
		return $this->snapshot;
	}

	public function get_change_reason(): ?string
	{
		return $this->change_reason;
	}

	public function get_created_by(): ?int
	{
		return $this->created_by;
	}

	public function get_created_at(): DateTime
	{
		return $this->created_at;
	}
}
