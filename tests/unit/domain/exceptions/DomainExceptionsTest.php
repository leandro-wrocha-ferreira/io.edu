<?php

use app\domain\exceptions\AppException;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\ForbiddenException;
use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\UnauthorizedException;
use app\domain\exceptions\ValidationException;

/**
 * Test suite for Domain Exceptions.
 */
class DomainExceptionsTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Test base AppException.
	 *
	 * @return void
	 */
	public function test_app_exception(): void
	{
		$exception = new AppException('Generic error', 400, ['field' => 'error']);
		$this->assertEquals(400, $exception->getStatusCode());
		$this->assertEquals('Generic error', $exception->getMessage());
		$this->assertEquals(['field' => 'error'], $exception->getErrors());
	}

	/**
	 * Test NotFoundException.
	 *
	 * @return void
	 */
	public function test_not_found_exception(): void
	{
		$exception = new NotFoundException('Record not found');
		$this->assertEquals(404, $exception->getStatusCode());
		$this->assertEquals('Record not found', $exception->getMessage());
	}

	/**
	 * Test ValidationException.
	 *
	 * @return void
	 */
	public function test_validation_exception(): void
	{
		$exception = new ValidationException('Invalid data', ['email' => 'invalid']);
		$this->assertEquals(422, $exception->getStatusCode());
		$this->assertEquals('Invalid data', $exception->getMessage());
		$this->assertEquals(['email' => 'invalid'], $exception->getErrors());
	}

	/**
	 * Test ConflictException.
	 *
	 * @return void
	 */
	public function test_conflict_exception(): void
	{
		$exception = new ConflictException('Already registered');
		$this->assertEquals(409, $exception->getStatusCode());
		$this->assertEquals('Already registered', $exception->getMessage());
	}

	/**
	 * Test ForbiddenException.
	 *
	 * @return void
	 */
	public function test_forbidden_exception(): void
	{
		$exception = new ForbiddenException('Access denied');
		$this->assertEquals(403, $exception->getStatusCode());
		$this->assertEquals('Access denied', $exception->getMessage());
	}

	/**
	 * Test UnauthorizedException.
	 *
	 * @return void
	 */
	public function test_unauthorized_exception(): void
	{
		$exception = new UnauthorizedException('Not authenticated');
		$this->assertEquals(401, $exception->getStatusCode());
		$this->assertEquals('Not authenticated', $exception->getMessage());
	}
}
