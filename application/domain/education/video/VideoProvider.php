<?php

namespace app\domain\education\video;

/**
 * Video Provider Entity mapping a provider configuration.
 */
class VideoProvider
{
	private ?int $id = null;
	private string $name;
	private string $driver;
	private ?array $credentials = null;

	/**
	 * Create a new VideoProvider.
	 *
	 * @param string $name
	 * @param string $driver
	 * @param array|null $credentials
	 * @return self
	 */
	public static function create(string $name, string $driver, ?array $credentials = null): self
	{
		$provider = new self();
		$provider->name = $name;
		$provider->driver = $driver;
		$provider->credentials = $credentials;
		return $provider;
	}

	/**
	 * Reconstitute from database.
	 *
	 * @param array $row
	 * @return self
	 */
	public static function from_database(array $row): self
	{
		$provider = new self();
		$provider->id = (int) $row['id'];
		$provider->name = $row['name'];
		$provider->driver = $row['driver'];
		$provider->credentials = isset($row['credentials']) ? json_decode($row['credentials'], true) : null;
		return $provider;
	}

	/**
	 * Get provider ID.
	 *
	 * @return int|null
	 */
	public function get_id(): ?int
	{
		return $this->id;
	}

	/**
	 * Set provider ID.
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
	 * Get provider name.
	 *
	 * @return string
	 */
	public function get_name(): string
	{
		return $this->name;
	}

	/**
	 * Set provider name.
	 *
	 * @param string $name
	 * @return self
	 */
	public function set_name(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Get provider driver.
	 *
	 * @return string
	 */
	public function get_driver(): string
	{
		return $this->driver;
	}

	/**
	 * Set provider driver.
	 *
	 * @param string $driver
	 * @return self
	 */
	public function set_driver(string $driver): self
	{
		$this->driver = $driver;
		return $this;
	}

	/**
	 * Get credentials.
	 *
	 * @return array|null
	 */
	public function get_credentials(): ?array
	{
		return $this->credentials;
	}

	/**
	 * Set credentials.
	 *
	 * @param array|null $credentials
	 * @return self
	 */
	public function set_credentials(?array $credentials): self
	{
		$this->credentials = $credentials;
		return $this;
	}
}
