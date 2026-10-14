<?php

namespace tests\unit\usecases\course;

use app\domain\course\CourseModule;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\GetCourseModuleUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseModuleRepository;

/**
 * Unit tests for GetCourseModuleUseCase.
 */
class GetCourseModuleUseCaseTest extends TestCase
{
	private MockCourseModuleRepository $module_repository;
	private GetCourseModuleUseCase $use_case;
	private CourseModule $module;

	protected function setUp(): void
	{
		parent::setUp();
		$this->module_repository = new MockCourseModuleRepository();
		$this->use_case = new GetCourseModuleUseCase($this->module_repository);

		$this->module = $this->module_repository->create(CourseModule::create(
			1,
			'Módulo Detalhe',
			'Descrição',
			1
		));
	}

	public function test_get_module_success(): void
	{
		$found = $this->use_case->execute((int) $this->module->get_id());

		$this->assertSame((int) $this->module->get_id(), $found->get_id());
		$this->assertSame('Módulo Detalhe', $found->get_title());
	}

	public function test_get_module_not_found_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999);
	}
}
