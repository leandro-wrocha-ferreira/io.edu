<?php

namespace tests\unit\usecases\course;

use app\domain\course\CourseModule;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\UpdateCourseModuleUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseModuleRepository;

/**
 * Unit tests for UpdateCourseModuleUseCase.
 */
class UpdateCourseModuleUseCaseTest extends TestCase
{
	private MockCourseModuleRepository $module_repository;
	private UpdateCourseModuleUseCase $use_case;
	private CourseModule $module;

	protected function setUp(): void
	{
		parent::setUp();
		$this->module_repository = new MockCourseModuleRepository();
		$this->use_case = new UpdateCourseModuleUseCase($this->module_repository);

		$this->module = $this->module_repository->create(CourseModule::create(
			1,
			'Módulo Inicial',
			'Descrição antiga',
			1,
			'draft'
		));
	}

	public function test_update_module_success(): void
	{
		$updated = $this->use_case->execute(
			(int) $this->module->get_id(),
			'Módulo Atualizado',
			'Nova descrição',
			'published',
			5
		);

		$this->assertSame('Módulo Atualizado', $updated->get_title());
		$this->assertSame('Nova descrição', $updated->get_description());
		$this->assertTrue($updated->is_published());
		$this->assertSame(5, $updated->get_sort_order());
	}

	public function test_update_module_not_found_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999, 'Novo Título');
	}

	public function test_update_module_allows_same_title_as_another_module(): void
	{
		$this->module_repository->create(CourseModule::create(
			1,
			'Outro Módulo',
			null,
			2
		));

		$updated = $this->use_case->execute((int) $this->module->get_id(), 'Outro Módulo');
		$this->assertSame('Outro Módulo', $updated->get_title());
	}
}
