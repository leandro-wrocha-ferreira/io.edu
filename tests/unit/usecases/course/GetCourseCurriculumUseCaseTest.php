<?php

namespace tests\unit\usecases\course;

use app\domain\course\blocks\VideoBlock;
use app\domain\course\constants\ContentStatus;
use app\domain\course\Course;
use app\domain\course\CourseLesson;
use app\domain\course\CourseModule;
use app\domain\course\LessonBlock;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CoursePrice;
use app\domain\course\value_objects\CourseSlug;
use app\domain\course\value_objects\CourseStatus;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\GetCourseCurriculumUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseLessonRepository;
use tests\unit\mocks\repositories\MockCourseModuleRepository;
use tests\unit\mocks\repositories\MockCourseRepository;
use tests\unit\mocks\repositories\MockLessonBlockRepository;

/**
 * Unit tests for GetCourseCurriculumUseCase.
 */
class GetCourseCurriculumUseCaseTest extends TestCase
{
	private MockCourseRepository $course_repository;
	private MockCourseModuleRepository $module_repository;
	private MockCourseLessonRepository $lesson_repository;
	private MockLessonBlockRepository $block_repository;
	private GetCourseCurriculumUseCase $use_case;
	private Course $course;

	protected function setUp(): void
	{
		parent::setUp();
		$this->course_repository = new MockCourseRepository();
		$this->module_repository = new MockCourseModuleRepository();
		$this->lesson_repository = new MockCourseLessonRepository();
		$this->block_repository = new MockLessonBlockRepository();

		$this->use_case = new GetCourseCurriculumUseCase(
			$this->course_repository,
			$this->module_repository,
			$this->lesson_repository,
			$this->block_repository
		);

		$this->course = $this->course_repository->create(Course::create(
			1,
			'Curso Completo de DDD',
			new CourseSlug('curso-completo-ddd'),
			new CourseStatus(CourseStatus::ACTIVE),
			new CourseAccessPeriod(CourseAccessPeriod::TYPE_LIFETIME)
		));

		$module_one = $this->module_repository->create(CourseModule::create(
			(int) $this->course->get_id(),
			'Módulo 1',
			'Desc 1',
			1,
			'published'
		));

		$module_two = $this->module_repository->create(CourseModule::create(
			(int) $this->course->get_id(),
			'Módulo 2 (Draft)',
			'Desc 2',
			2,
			'draft'
		));

		$lesson_one = $this->lesson_repository->create(CourseLesson::create(
			(int) $module_one->get_id(),
			'Aula 1.1',
			'Sinopse',
			1,
			'published'
		));

		$this->block_repository->create(VideoBlock::create_video(
			(int) $lesson_one->get_id(),
			'vimeo',
			'https://vimeo.com/123',
			600,
			1
		));

		$this->block_repository->create(LessonBlock::create(
			(int) $lesson_one->get_id(),
			'text',
			['content' => 'Resumo'],
			2
		));

		$lesson_two = $this->lesson_repository->create(CourseLesson::create(
			(int) $module_two->get_id(),
			'Aula 2.1',
			null,
			1,
			'draft'
		));

		$this->block_repository->create(VideoBlock::create_video(
			(int) $lesson_two->get_id(),
			'youtube',
			'https://youtube.com/watch?v=1',
			300,
			1
		));
	}

	public function test_get_curriculum_all_success(): void
	{
		$result = $this->use_case->execute((int) $this->course->get_id());

		$this->assertSame((int) $this->course->get_id(), $result['course']->get_id());
		$this->assertCount(2, $result['modules']);
		$this->assertSame(2, $result['metrics']['total_modules']);
		$this->assertSame(2, $result['metrics']['total_lessons']);
		$this->assertSame(900, $result['metrics']['total_duration_seconds']);

		$first_module = $result['modules'][0];
		$this->assertCount(1, $first_module->get_lessons());
		$first_lesson = $first_module->get_lessons()[0];
		$this->assertCount(2, $first_lesson->get_blocks());
	}

	public function test_get_curriculum_only_published_success(): void
	{
		$result = $this->use_case->execute((int) $this->course->get_id(), true);

		$this->assertCount(1, $result['modules']);
		$this->assertSame(1, $result['metrics']['total_modules']);
		$this->assertSame(1, $result['metrics']['total_lessons']);
		$this->assertSame(600, $result['metrics']['total_duration_seconds']);
	}

	public function test_get_curriculum_course_not_found_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999);
	}
}
