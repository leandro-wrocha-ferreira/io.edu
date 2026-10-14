<?php

namespace tests\unit\usecases\course;

use app\domain\course\CourseModule;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\DeleteCourseModuleUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseModuleRepository;

/**
 * Unit tests for DeleteCourseModuleUseCase.
 */
class DeleteCourseModuleUseCaseTest extends TestCase
{
	private MockCourseModuleRepository $module_repository;
	private DeleteCourseModuleUseCase $use_case;
	private CourseModule $module;

	protected function setUp(): void
	{
		parent::setUp();
		$this->module_repository = new MockCourseModuleRepository();
		$this->use_case = new DeleteCourseModuleUseCase($this->module_repository);

		$this->module = $this->module_repository->create(CourseModule::create(
			1,
			'Módulo para Excluir',
			null,
			1
		));
	}

	public function test_delete_module_success(): void
	{
		$deleted = $this->use_case->execute((int) $this->module->get_id());

		$this->assertTrue($deleted->is_deleted());
		$this->assertNull($this->module_repository->find_by_id((int) $this->module->get_id()));
	}

	public function test_delete_module_not_found_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999);
	}
}
