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
		$exception = new AppException('Erro genérico', 400, ['field' => 'erro']);
		$this->assertEquals(400, $exception->getStatusCode());
		$this->assertEquals('Erro genérico', $exception->getMessage());
		$this->assertEquals(['field' => 'erro'], $exception->getErrors());
	}

	/**
	 * Test NotFoundException.
	 *
	 * @return void
	 */
	public function test_not_found_exception(): void
	{
		$exception = new NotFoundException('Registro não encontrado');
		$this->assertEquals(404, $exception->getStatusCode());
		$this->assertEquals('Registro não encontrado', $exception->getMessage());
	}

	/**
	 * Test ValidationException.
	 *
	 * @return void
	 */
	public function test_validation_exception(): void
	{
		$exception = new ValidationException('Dados inválidos', ['email' => 'invalido']);
		$this->assertEquals(422, $exception->getStatusCode());
		$this->assertEquals('Dados inválidos', $exception->getMessage());
		$this->assertEquals(['email' => 'invalido'], $exception->getErrors());
	}

	/**
	 * Test ConflictException.
	 *
	 * @return void
	 */
	public function test_conflict_exception(): void
	{
		$exception = new ConflictException('Já cadastrado');
		$this->assertEquals(409, $exception->getStatusCode());
		$this->assertEquals('Já cadastrado', $exception->getMessage());
	}

	/**
	 * Test ForbiddenException.
	 *
	 * @return void
	 */
	public function test_forbidden_exception(): void
	{
		$exception = new ForbiddenException('Acesso negado');
		$this->assertEquals(403, $exception->getStatusCode());
		$this->assertEquals('Acesso negado', $exception->getMessage());
	}

	/**
	 * Test UnauthorizedException.
	 *
	 * @return void
	 */
	public function test_unauthorized_exception(): void
	{
		$exception = new UnauthorizedException('Não autenticado');
		$this->assertEquals(401, $exception->getStatusCode());
		$this->assertEquals('Não autenticado', $exception->getMessage());
	}
}
