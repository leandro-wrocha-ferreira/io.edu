<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Courses Public Controller
 *
 * Handles public course catalog discovery, faceted filtering,
 * and comprehensive course detail landing pages.
 */
class Courses extends MY_Controller
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Course catalog & discovery page.
	 *
	 * Displays hero search, category filters, and featured course grid.
	 *
	 * @return void
	 */
	public function index()
	{
		$data = [
			'title' => 'Catálogo de Cursos & Formações — ' . get_institution_name(),
			'page_name' => 'public/catalog',
		];

		$this->load->view('layout/public', $data);
	}

	/**
	 * Course details & syllabus presentation page.
	 *
	 * Presents learning outcomes, curriculum accordion, and enrollment actions.
	 *
	 * @param string $slug Identifier or slug of the course
	 * @return void
	 */
	public function detail(string $slug = 'desenvolvimento-web-moderno')
	{
		$data = [
			'title' => 'Desenvolvimento Web Moderno com Arquitetura Limpa — ' . get_institution_name(),
			'page_name' => 'public/courses/detail',
			'course_slug' => $slug,
		];

		$this->load->view('layout/public', $data);
	}
}
