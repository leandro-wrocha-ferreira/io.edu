<?php

namespace tests\unit\domain\identity;

use app\domain\identity\value_objects\Password;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for Password Value Object.
 */
class PasswordTest extends TestCase
{
	/**
	 * Test creating a password from plain text.
	 *
	 * @return void
	 */
	public function test_create_from_plain_text(): void
	{
		$password = new Password('secret123');
		$this->assertNotEmpty($password->get_value());
		$this->assertTrue($password->verify('secret123'));
		$this->assertFalse($password->verify('wrong_password'));
	}

	/**
	 * Test creating empty password throws exception.
	 *
	 * @return void
	 */
	public function test_empty_password_throws_exception(): void
	{
		$this->expectException(InvalidArgumentException::class);
		new Password('');
	}

	/**
	 * Test creating password from existing hash.
	 *
	 * @return void
	 */
	public function test_create_from_existing_hash(): void
	{
		$hash = password_hash('my_pass', PASSWORD_BCRYPT);
		$password = Password::from_hash($hash);

		$this->assertSame($hash, $password->get_value());
		$this->assertTrue($password->verify('my_pass'));
	}

	/**
	 * Test static factory from_plain_text.
	 *
	 * @return void
	 */
	public function test_from_plain_text_factory(): void
	{
		$password = Password::from_plain_text('hello_world');
		$this->assertTrue($password->verify('hello_world'));
	}

	/**
	 * Test __toString returns the hash value.
	 *
	 * @return void
	 */
	public function test_to_string(): void
	{
		$hash = password_hash('test', PASSWORD_BCRYPT);
		$password = new Password($hash);
		$this->assertSame($hash, (string) $password);
	}

	/**
	 * Test equals comparison.
	 *
	 * @return void
	 */
	public function test_equals(): void
	{
		$hash1 = password_hash('pwd1', PASSWORD_BCRYPT);
		$hash2 = password_hash('pwd2', PASSWORD_BCRYPT);

		$password1_a = new Password($hash1);
		$password1_b = new Password($hash1);
		$password2 = new Password($hash2);

		$this->assertTrue($password1_a->equals($password1_b));
		$this->assertFalse($password1_a->equals($password2));
	}
}
