<?php

namespace app\usecases\course;

use app\domain\course\Course;
use app\domain\course\CourseModule;
use app\domain\course\repositories\CourseLessonRepositoryInterface;
use app\domain\course\repositories\CourseModuleRepositoryInterface;
use app\domain\course\repositories\CourseRepositoryInterface;
use app\domain\course\repositories\LessonBlockRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for retrieving the complete curriculum tree of a course (Course -> Modules -> Lessons -> Blocks).
 */
class GetCourseCurriculumUseCase
{
	/** @var CourseRepositoryInterface */
	private CourseRepositoryInterface $course_repository;

	/** @var CourseModuleRepositoryInterface */
	private CourseModuleRepositoryInterface $module_repository;

	/** @var CourseLessonRepositoryInterface */
	private CourseLessonRepositoryInterface $lesson_repository;

	/** @var LessonBlockRepositoryInterface */
	private LessonBlockRepositoryInterface $block_repository;

	/**
	 * Constructor.
	 *
	 * @param CourseRepositoryInterface $course_repository
	 * @param CourseModuleRepositoryInterface $module_repository
	 * @param CourseLessonRepositoryInterface $lesson_repository
	 * @param LessonBlockRepositoryInterface $block_repository
	 */
	public function __construct(
		CourseRepositoryInterface $course_repository,
		CourseModuleRepositoryInterface $module_repository,
		CourseLessonRepositoryInterface $lesson_repository,
		LessonBlockRepositoryInterface $block_repository
	)
	{
		$this->course_repository = $course_repository;
		$this->module_repository = $module_repository;
		$this->lesson_repository = $lesson_repository;
		$this->block_repository = $block_repository;
	}

	/**
	 * Execute curriculum tree assembly.
	 *
	 * @param int $course_id
	 * @param bool $only_published If true, filter only published modules and lessons
	 * @return array{course: Course, modules: array<CourseModule>, metrics: array{total_modules: int, total_lessons: int, total_duration_seconds: int}}
	 * @throws NotFoundException If course not found
	 */
	public function execute(int $course_id, bool $only_published = false): array
	{
		$course = $this->course_repository->find_by_id($course_id);
		if ($course === null) {
			throw new NotFoundException("Course not found");
		}

		$modules = $this->module_repository->find_by_course_id($course_id, $only_published);
		$total_lessons = 0;
		$total_duration_seconds = 0;

		foreach ($modules as $module) {
			if ($module->get_id() === null) {
				continue;
			}

			$lessons = $this->lesson_repository->find_by_module_id($module->get_id(), $only_published);
			foreach ($lessons as $lesson) {
				if ($lesson->get_id() !== null) {
					$blocks = $this->block_repository->find_by_lesson_id($lesson->get_id());
					$lesson->set_blocks($blocks);
				}
				$total_lessons++;
				$total_duration_seconds += $lesson->get_total_duration_in_seconds();
			}

			$module->set_lessons($lessons);
		}

		return [
			'course' => $course,
			'modules' => $modules,
			'metrics' => [
				'total_modules' => count($modules),
				'total_lessons' => $total_lessons,
				'total_duration_seconds' => $total_duration_seconds,
			],
		];
	}
}
