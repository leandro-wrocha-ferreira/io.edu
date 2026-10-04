<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Courses Administration Controller
 *
 * Handles management of courses, detailed view, visual editing,
 * curriculum content hierarchy (modules, sections, lessons), and lesson editor.
 * Note: Visual prototype presentation layer; uses isolated presentation fixtures.
 */
class Courses extends MY_Controller
{
	/**
	 * List all courses with operational search, filters, and metrics.
	 *
	 * @return void
	 */
	public function index()
	{
		$data = [
			'title' => 'Gerenciamento de Cursos',
			'page_name' => 'admin/courses/index',
			'courses' => get_mock_admin_courses(),
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display comprehensive detail view for a specific course.
	 *
	 * @param int $course_id Course identifier
	 * @return void
	 */
	public function detail($course_id = 1)
	{
		$course_id = (int) $course_id;
		$course = get_mock_course_by_id($course_id);

		$data = [
			'title' => $course['title'],
			'page_name' => 'admin/courses/detail',
			'course' => $course,
			'curriculum' => get_mock_course_curriculum($course_id),
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display visual creation form for a new course.
	 *
	 * @return void
	 */
	public function create()
	{
		$data = [
			'title' => 'Novo Curso',
			'page_name' => 'admin/courses/form',
			'course' => null,
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display visual editing form for an existing course.
	 *
	 * @param int $course_id Course identifier
	 * @return void
	 */
	public function edit($course_id = 1)
	{
		$course_id = (int) $course_id;
		$course = get_mock_course_by_id($course_id);

		$data = [
			'title' => 'Editar Curso — ' . $course['title'],
			'page_name' => 'admin/courses/form',
			'course' => $course,
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display curriculum content hierarchy (Modules -> Sections -> Lessons).
	 *
	 * @param int $course_id Course identifier
	 * @return void
	 */
	public function content($course_id = 1)
	{
		$course_id = (int) $course_id;
		$course = get_mock_course_by_id($course_id);

		$data = [
			'title' => 'Conteúdo Curricular — ' . $course['title'],
			'page_name' => 'admin/courses/content',
			'course' => $course,
			'curriculum' => get_mock_course_curriculum($course_id),
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display visual lesson editor for course content.
	 *
	 * @param int $course_id Course identifier
	 * @return void
	 */
	public function lesson_editor($course_id = 1)
	{
		$course_id = (int) $course_id;
		$course = get_mock_course_by_id($course_id);

		$data = [
			'title' => 'Editor de Aula — ' . $course['title'],
			'page_name' => 'admin/courses/lesson_editor',
			'course' => $course,
		];

		$this->load->view('layout/admin', $data);
	}
}
