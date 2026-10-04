<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a URL
| normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'courses';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Auth routes
$route['entrar'] = 'auth/login';
$route['sair'] = 'auth/logout';

// Admin routes
$route['admin/painel'] = 'admin/dashboard/index';

// Usuários
$route['admin/usuarios'] = 'admin/users/index';
$route['admin/usuarios/dados'] = 'admin/users/ajax_data';
$route['admin/usuarios/novo'] = 'admin/users/create';
$route['admin/usuarios/editar/(:num)'] = 'admin/users/update/$1';
$route['admin/usuarios/ativar/(:num)'] = 'admin/users/activate/$1';
$route['admin/usuarios/desativar/(:num)'] = 'admin/users/disable/$1';
$route['admin/usuarios/excluir/(:num)'] = 'admin/users/delete/$1';

// Perfis e Permissões
$route['admin/perfis'] = 'admin/roles/index';
$route['admin/perfis/dados'] = 'admin/roles/ajax_data';
$route['admin/perfis/novo'] = 'admin/roles/create';
$route['admin/perfis/editar/(:num)'] = 'admin/roles/update/$1';
$route['admin/perfis/excluir/(:num)'] = 'admin/roles/delete/$1';

// Cursos
$route['admin/cursos'] = 'admin/courses/index';
$route['admin/cursos/novo'] = 'admin/courses/create';
$route['admin/cursos/(:num)'] = 'admin/courses/detail/$1';
$route['admin/cursos/(:num)/editar'] = 'admin/courses/edit/$1';
$route['admin/cursos/(:num)/modulos'] = 'admin/courses/content/$1';
$route['admin/cursos/(:num)/conteudo'] = 'admin/courses/content/$1';
$route['admin/cursos/(:num)/aulas/editor'] = 'admin/courses/lesson_editor/$1';

// Turmas
$route['admin/turmas'] = 'admin/classes/index';
$route['admin/turmas/novo'] = 'admin/classes/create';
$route['admin/turmas/(:num)'] = 'admin/classes/detail/$1';

// Avaliações
$route['admin/avaliacoes'] = 'admin/evaluations/index';
$route['admin/avaliacoes/novo'] = 'admin/evaluations/create';
$route['admin/avaliacoes/(:num)'] = 'admin/evaluations/detail/$1';
$route['admin/avaliacoes/(:num)/editar'] = 'admin/evaluations/edit/$1';

// Relatórios
$route['admin/relatorios'] = 'admin/reports/academic';
$route['admin/relatorios/academicos'] = 'admin/reports/academic';
$route['admin/relatorios/financeiros'] = 'admin/reports/financial';

// Student routes
$route['aluno/painel'] = 'student/dashboard/index';
$route['aluno/jornadas'] = 'student/dashboard/journeys';
$route['aluno/aula/(:any)'] = 'student/dashboard/classroom/$1';

// Public Catalog routes
$route['cursos'] = 'courses/index';
$route['cursos/detalhes/(:any)'] = 'courses/detail/$1';
