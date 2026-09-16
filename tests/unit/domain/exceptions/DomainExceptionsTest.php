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
	public function test_app_exception(): void
	{
		$e = new AppException('Erro genérico', 400, ['field' => 'erro']);
		$this->assertEquals(400, $e->getStatusCode());
		$this->assertEquals('Erro genérico', $e->getMessage());
		$this->assertEquals(['field' => 'erro'], $e->getErrors());
	}

	public function test_not_found_exception(): void
	{
		$e = new NotFoundException('Registro não encontrado');
		$this->assertEquals(404, $e->getStatusCode());
		$this->assertEquals('Registro não encontrado', $e->getMessage());
	}

	public function test_validation_exception(): void
	{
		$e = new ValidationException('Dados inválidos', ['email' => 'invalido']);
		$this->assertEquals(422, $e->getStatusCode());
		$this->assertEquals('Dados inválidos', $e->getMessage());
		$this->assertEquals(['email' => 'invalido'], $e->getErrors());
	}

	public function test_conflict_exception(): void
	{
		$e = new ConflictException('Já cadastrado');
		$this->assertEquals(409, $e->getStatusCode());
		$this->assertEquals('Já cadastrado', $e->getMessage());
	}

	public function test_forbidden_exception(): void
	{
		$e = new ForbiddenException('Acesso negado');
		$this->assertEquals(403, $e->getStatusCode());
		$this->assertEquals('Acesso negado', $e->getMessage());
	}

	public function test_unauthorized_exception(): void
	{
		$e = new UnauthorizedException('Não autenticado');
		$this->assertEquals(401, $e->getStatusCode());
		$this->assertEquals('Não autenticado', $e->getMessage());
	}
}
