<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Prototype Fixtures Helper — io.edu LMS
 *
 * Provides structured presentation mock data for Phase 4 visual interfaces:
 * Courses, Classes, Content Hierarchy, Evaluations, and Reports.
 *
 * NOTE: This data is strictly for visual presentation and UI prototypes.
 * It is completely isolated from repositories, models, entities, and database.
 */

if (!function_exists('get_mock_admin_courses'))
{
	/**
	 * Retrieve mock course list for admin management.
	 *
	 * @return array
	 */
	function get_mock_admin_courses(): array
	{
		return [
			[
				'id' => 1,
				'title' => 'Desenvolvimento Web Fullstack',
				'category' => 'Tecnologia',
				'instructor' => 'Prof. Marcelo Santos',
				'workload' => '120h',
				'status' => 'published',
				'status_label' => 'Publicado',
				'students_count' => 128,
				'modules_count' => 6,
				'lessons_count' => 42,
				'evaluations_count' => 4,
				'updated_at' => 'Hoje, 14:30',
				'description' => 'Formação completa em desenvolvimento de aplicações modernas com PHP 8, JavaScript moderno, bancos relacionais e arquitetura de software.',
			],
			[
				'id' => 2,
				'title' => 'Gestão Ágil e Scrum',
				'category' => 'Gestão & Negócios',
				'instructor' => 'Profa. Camila Duarte',
				'workload' => '40h',
				'status' => 'published',
				'status_label' => 'Publicado',
				'students_count' => 94,
				'modules_count' => 4,
				'lessons_count' => 26,
				'evaluations_count' => 2,
				'updated_at' => 'Ontem, 09:15',
				'description' => 'Domine cerimônias ágeis, escrita de estórias de usuário, métricas de sprint e liderança servidora em equipes de alto desempenho.',
			],
			[
				'id' => 3,
				'title' => 'Segurança da Informação e LGPD',
				'category' => 'Segurança',
				'instructor' => 'Prof. André Ribeiro',
				'workload' => '60h',
				'status' => 'review',
				'status_label' => 'Em Revisão',
				'students_count' => 62,
				'modules_count' => 5,
				'lessons_count' => 34,
				'evaluations_count' => 3,
				'updated_at' => '28/09/2026',
				'description' => 'Fundamentos de cibersegurança corporativa, mitigação de vulnerabilidades OWASP e adequação jurídica à LGPD.',
			],
			[
				'id' => 4,
				'title' => 'Liderança e Comunicação Não-Violenta',
				'category' => 'Soft Skills',
				'instructor' => 'Profa. Beatriz Lima',
				'workload' => '30h',
				'status' => 'published',
				'status_label' => 'Publicado',
				'students_count' => 115,
				'modules_count' => 3,
				'lessons_count' => 18,
				'evaluations_count' => 2,
				'updated_at' => '25/09/2026',
				'description' => 'Desenvolva habilidades de escuta ativa, resolução de conflitos interpessoais e feedback construtivo na liderança.',
			],
			[
				'id' => 5,
				'title' => 'Data Science e Machine Learning com Python',
				'category' => 'Tecnologia',
				'instructor' => 'Prof. Lucas Mendes',
				'workload' => '90h',
				'status' => 'draft',
				'status_label' => 'Rascunho',
				'students_count' => 0,
				'modules_count' => 8,
				'lessons_count' => 52,
				'evaluations_count' => 5,
				'updated_at' => '20/09/2026',
				'description' => 'Análise exploratória de dados com Pandas, modelagem preditiva com Scikit-Learn e visualização estatística interativa.',
			],
		];
	}
}

if (!function_exists('get_mock_course_by_id'))
{
	/**
	 * Retrieve a specific course by ID.
	 *
	 * @param int $id Course ID
	 * @return array
	 */
	function get_mock_course_by_id(int $id): array
	{
		$courses = get_mock_admin_courses();
		foreach ($courses as $course) {
			if ($course['id'] === $id) {
				return $course;
			}
		}
		return $courses[0]; // fallback to first course
	}
}

if (!function_exists('get_mock_course_curriculum'))
{
	/**
	 * Retrieve structured curriculum (Modules -> Sections -> Lessons) for a course.
	 *
	 * @param int $course_id Course ID
	 * @return array
	 */
	function get_mock_course_curriculum(int $course_id = 1): array
	{
		return [
			[
				'id' => 1,
				'order' => 1,
				'title' => 'Fundamentos e Arquitetura Web',
				'duration' => '12h 40min',
				'lessons_count' => 8,
				'status' => 'published',
				'sections' => [
					[
						'id' => 101,
						'title' => 'Introdução e Configuração do Ambiente',
						'lessons' => [
							[
								'id' => 1001,
								'order' => 1,
								'title' => 'Boas-vindas e Visão Geral da Formação',
								'duration' => '14:20',
								'type' => 'video',
								'status' => 'published',
								'materials_count' => 2,
								'required' => true,
							],
							[
								'id' => 1002,
								'order' => 2,
								'title' => 'Configurando Docker e PHP 8.2 Localmente',
								'duration' => '22:15',
								'type' => 'video',
								'status' => 'published',
								'materials_count' => 4,
								'required' => true,
							],
							[
								'id' => 1003,
								'order' => 3,
								'title' => 'Controle de Versão com Git e Fluxos de Branch',
								'duration' => '18:40',
								'type' => 'video',
								'status' => 'published',
								'materials_count' => 1,
								'required' => false,
							],
						],
					],
					[
						'id' => 102,
						'title' => 'HTML5 Semântico e Acessibilidade (WCAG)',
						'lessons' => [
							[
								'id' => 1004,
								'order' => 4,
								'title' => 'Estrutura Semântica e Hierarquia de Cabeçalhos',
								'duration' => '25:10',
								'type' => 'video',
								'status' => 'published',
								'materials_count' => 3,
								'required' => true,
							],
							[
								'id' => 1005,
								'order' => 5,
								'title' => 'Diretrizes WCAG 2.1 AA na Prática',
								'duration' => '30:00',
								'type' => 'video',
								'status' => 'published',
								'materials_count' => 2,
								'required' => true,
							],
						],
					],
				],
			],
			[
				'id' => 2,
				'order' => 2,
				'title' => 'Estilização Moderna com Design Tokens & CSS',
				'duration' => '24h 15min',
				'lessons_count' => 10,
				'status' => 'published',
				'sections' => [
					[
						'id' => 201,
						'title' => 'Flexbox e Grid Layout Responsivo',
						'lessons' => [
							[
								'id' => 2001,
								'order' => 6,
								'title' => 'Flexbox Avançado para Interfaces SaaS',
								'duration' => '28:15',
								'type' => 'video',
								'status' => 'published',
								'materials_count' => 2,
								'required' => true,
							],
							[
								'id' => 2002,
								'order' => 7,
								'title' => 'CSS Grid para Dashboards e Tabelas de Dados',
								'duration' => '32:40',
								'type' => 'video',
								'status' => 'published',
								'materials_count' => 3,
								'required' => true,
							],
						],
					],
					[
						'id' => 202,
						'title' => 'Sistemas de Temas e White-Label',
						'lessons' => [
							[
								'id' => 2003,
								'order' => 8,
								'title' => 'Design Tokens e Variáveis CSS Reutilizáveis',
								'duration' => '20:00',
								'type' => 'video',
								'status' => 'published',
								'materials_count' => 1,
								'required' => true,
							],
							[
								'id' => 2004,
								'order' => 9,
								'title' => 'Implementando Light e Dark Mode sem FOUC',
								'duration' => '26:30',
								'type' => 'video',
								'status' => 'published',
								'materials_count' => 2,
								'required' => false,
							],
						],
					],
				],
			],
			[
				'id' => 3,
				'order' => 3,
				'title' => 'Backend Robusto com PHP 8 e DDD-Lite',
				'duration' => '36h 20min',
				'lessons_count' => 12,
				'status' => 'published',
				'sections' => [
					[
						'id' => 301,
						'title' => 'Padrões de Domínio e Casos de Uso',
						'lessons' => [
							[
								'id' => 3001,
								'order' => 10,
								'title' => 'Entidades, Value Objects e Exceções Semânticas',
								'duration' => '35:00',
								'type' => 'video',
								'status' => 'published',
								'materials_count' => 4,
								'required' => true,
							],
							[
								'id' => 3002,
								'order' => 11,
								'title' => 'Use Cases e Injeção de Dependências com Model Factory',
								'duration' => '42:15',
								'type' => 'video',
								'status' => 'published',
								'materials_count' => 3,
								'required' => true,
							],
						],
					],
				],
			],
		];
	}
}

if (!function_exists('get_mock_admin_classes'))
{
	/**
	 * Retrieve mock class cohorts for admin management.
	 *
	 * @return array
	 */
	function get_mock_admin_classes(): array
	{
		return [
			[
				'id' => 1,
				'name' => 'Turma Web 2026.2',
				'course_title' => 'Desenvolvimento Web Fullstack',
				'course_id' => 1,
				'instructor' => 'Marcelo Santos',
				'period' => '01/08/2026 — 30/11/2026',
				'students_count' => 32,
				'status' => 'active',
				'status_label' => 'Ativa',
				'progress_avg' => 82,
				'attendance_rate' => 94,
			],
			[
				'id' => 2,
				'name' => 'Turma Gestão Ágil 2026.1',
				'course_title' => 'Gestão Ágil e Scrum',
				'course_id' => 2,
				'instructor' => 'Camila Duarte',
				'period' => '15/07/2026 — 15/10/2026',
				'students_count' => 28,
				'status' => 'active',
				'status_label' => 'Ativa',
				'progress_avg' => 64,
				'attendance_rate' => 88,
			],
			[
				'id' => 3,
				'name' => 'Turma Segurança Corporativa Q3',
				'course_title' => 'Segurança da Informação e LGPD',
				'course_id' => 3,
				'instructor' => 'André Ribeiro',
				'period' => '01/09/2026 — 15/12/2026',
				'students_count' => 24,
				'status' => 'planned',
				'status_label' => 'Prevista',
				'progress_avg' => 0,
				'attendance_rate' => 0,
			],
			[
				'id' => 4,
				'name' => 'Turma Liderança Executiva 2026',
				'course_title' => 'Liderança e Comunicação Não-Violenta',
				'course_id' => 4,
				'instructor' => 'Beatriz Lima',
				'period' => '10/05/2026 — 10/08/2026',
				'students_count' => 30,
				'status' => 'finished',
				'status_label' => 'Concluída',
				'progress_avg' => 96,
				'attendance_rate' => 98,
			],
		];
	}
}

if (!function_exists('get_mock_class_by_id'))
{
	/**
	 * Retrieve a specific class cohort by ID.
	 *
	 * @param int $id Class ID
	 * @return array
	 */
	function get_mock_class_by_id(int $id): array
	{
		$classes = get_mock_admin_classes();
		foreach ($classes as $cohort) {
			if ($cohort['id'] === $id) {
				return $cohort;
			}
		}
		return $classes[0];
	}
}

if (!function_exists('get_mock_admin_evaluations'))
{
	/**
	 * Retrieve mock evaluations for admin management.
	 *
	 * @return array
	 */
	function get_mock_admin_evaluations(): array
	{
		return [
			[
				'id' => 1,
				'title' => 'Avaliação Final — Módulo 1 Fundamentos',
				'course_title' => 'Desenvolvimento Web Fullstack',
				'module_title' => 'Módulo 1 · Fundamentos',
				'type' => 'multiple_choice',
				'type_label' => 'Múltipla Escolha',
				'questions_count' => 10,
				'attempts_count' => 156,
				'average_score' => '8,4',
				'status' => 'published',
				'status_label' => 'Publicada',
				'time_limit' => '45 min',
			],
			[
				'id' => 2,
				'title' => 'Prova Prática de CSS Grid e Design Tokens',
				'course_title' => 'Desenvolvimento Web Fullstack',
				'module_title' => 'Módulo 2 · CSS e Tokens',
				'type' => 'mixed',
				'type_label' => 'Mista / Prática',
				'questions_count' => 8,
				'attempts_count' => 112,
				'average_score' => '7,9',
				'status' => 'published',
				'status_label' => 'Publicada',
				'time_limit' => '60 min',
			],
			[
				'id' => 3,
				'title' => 'Questionário de Cerimônias e Métricas Ágeis',
				'course_title' => 'Gestão Ágil e Scrum',
				'module_title' => 'Módulo 2 · Cerimônias',
				'type' => 'multiple_choice',
				'type_label' => 'Múltipla Escolha',
				'questions_count' => 15,
				'attempts_count' => 84,
				'average_score' => '8,9',
				'status' => 'published',
				'status_label' => 'Publicada',
				'time_limit' => '50 min',
			],
			[
				'id' => 4,
				'title' => 'Simulado de Segurança e Mitigação OWASP',
				'course_title' => 'Segurança da Informação e LGPD',
				'module_title' => 'Módulo 1 · Cibersegurança',
				'type' => 'multiple_choice',
				'type_label' => 'Múltipla Escolha',
				'questions_count' => 12,
				'attempts_count' => 45,
				'average_score' => '7,2',
				'status' => 'review',
				'status_label' => 'Em Revisão',
				'time_limit' => '40 min',
			],
		];
	}
}

if (!function_exists('get_mock_evaluation_by_id'))
{
	/**
	 * Retrieve a specific evaluation by ID.
	 *
	 * @param int $id Evaluation ID
	 * @return array
	 */
	function get_mock_evaluation_by_id(int $id): array
	{
		$evaluations = get_mock_admin_evaluations();
		foreach ($evaluations as $item) {
			if ($item['id'] === $id) {
				return $item;
			}
		}
		return $evaluations[0];
	}
}

if (!function_exists('get_mock_academic_reports'))
{
	/**
	 * Retrieve academic metrics and charts data.
	 *
	 * @return array
	 */
	function get_mock_academic_reports(): array
	{
		return [
			'kpis' => [
				'active_students' => '1.284',
				'active_courses' => '24',
				'avg_completion_rate' => '72%',
				'dropout_rate' => '18%',
			],
			'course_completion' => [
				['course' => 'Liderança e Comunicação', 'rate' => 91, 'badge' => 'Alta'],
				['course' => 'Desenvolvimento Web Fullstack', 'rate' => 82, 'badge' => 'Alta'],
				['course' => 'Gestão Ágil e Scrum', 'rate' => 74, 'badge' => 'Média'],
				['course' => 'Segurança da Informação e LGPD', 'rate' => 68, 'badge' => 'Atenção'],
			],
			'dropout_analysis' => [
				'overall_rate' => '18%',
				'critical_courses' => [
					['course' => 'Segurança da Informação', 'rate' => '27%'],
					['course' => 'Data Science com Python', 'rate' => '24%'],
					['course' => 'Gestão Ágil e Scrum', 'rate' => '21%'],
				],
				'dropout_points' => [
					['module' => 'Módulo 1 · Introdução', 'rate' => '4%'],
					['module' => 'Módulo 2 · Intermediário', 'rate' => '8%'],
					['module' => 'Módulo 3 · Práticas Avançadas', 'rate' => '16%'],
					['module' => 'Módulo 4 · Projeto Final', 'rate' => '22%'],
				],
			],
			'cohort_performance' => [
				['cohort' => 'Turma Web 2026.2', 'students' => 32, 'completion' => '82%', 'grade_avg' => '8,4'],
				['cohort' => 'Turma Gestão Ágil 2026.1', 'students' => 28, 'completion' => '74%', 'grade_avg' => '8,9'],
				['cohort' => 'Turma Liderança Executiva', 'students' => 30, 'completion' => '96%', 'grade_avg' => '9,2'],
			],
		];
	}
}

if (!function_exists('get_mock_financial_reports'))
{
	/**
	 * Retrieve financial performance data.
	 *
	 * @return array
	 */
	function get_mock_financial_reports(): array
	{
		return [
			'kpis' => [
				'total_revenue' => 'R$ 184.500',
				'sales_count' => '1.284',
				'average_ticket' => 'R$ 143,68',
				'recurring_ratio' => '32%',
			],
			'payment_methods' => [
				['method' => 'Cartão de Crédito', 'percentage' => 64, 'total' => 'R$ 118.080'],
				['method' => 'PIX Instantâneo', 'percentage' => 28, 'total' => 'R$ 51.660'],
				['method' => 'Boleto Bancário', 'percentage' => 8, 'total' => 'R$ 14.760'],
			],
			'recent_transactions' => [
				['id' => 'TX-9012', 'student' => 'Mariana Souza', 'course' => 'Desenvolvimento Web Fullstack', 'date' => '01/10/2026', 'amount' => 'R$ 189,00', 'method' => 'Cartão de Crédito', 'status' => 'approved'],
				['id' => 'TX-9011', 'student' => 'Rodrigo Albuquerque', 'course' => 'Gestão Ágil e Scrum', 'date' => '01/10/2026', 'amount' => 'R$ 149,00', 'method' => 'PIX', 'status' => 'approved'],
				['id' => 'TX-9010', 'student' => 'Patrícia Guimarães', 'course' => 'Liderança e Comunicação', 'date' => '30/09/2026', 'amount' => 'R$ 129,00', 'method' => 'Cartão de Crédito', 'status' => 'approved'],
				['id' => 'TX-9009', 'student' => 'Felipe Cardoso', 'course' => 'Segurança da Informação', 'date' => '30/09/2026', 'amount' => 'R$ 179,00', 'method' => 'Boleto Bancário', 'status' => 'pending'],
				['id' => 'TX-9008', 'student' => 'Juliana Ferreira', 'course' => 'Desenvolvimento Web Fullstack', 'date' => '29/09/2026', 'amount' => 'R$ 189,00', 'method' => 'PIX', 'status' => 'approved'],
			],
		];
	}
}
