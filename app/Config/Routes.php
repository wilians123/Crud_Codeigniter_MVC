<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
//RUTAS DE ALUMNOS
$routes->get('/alumnos', 'AlumnoController::index');
$routes->get('/alumnos/create', 'AlumnoController::create');
$routes->post('/alumnos/store', 'AlumnoController::store');
$routes->get('/alumnos/edit/(:num)', 'AlumnoController::edit/$1');
$routes->post('/alumnos/update/(:num)', 'AlumnoController::update/$1');
$routes->get('/alumnos/delete/(:num)', 'AlumnoController::delete/$1');
$routes->post('/alumnos/asignarCursos', 'AlumnoController::asignarCursos');
$routes->get('/alumnos/verCursosAsignados/(:num)', 'AlumnoController::verCursosAsignados/$1');
$routes->get('/debug/cursos/(:num)', 'AlumnoController::debugCursos/$1');

//RUTAS DE CURSOS
$routes->get('/cursos', 'CursoController::index');
$routes->get('/cursos/create', 'CursoController::create');
$routes->post('/cursos/store', 'CursoController::store');
$routes->get('/cursos/edit/(:num)', 'CursoController::edit/$1');
$routes->post('/cursos/update/(:num)', 'CursoController::update/$1');
$routes->get('/cursos/delete/(:num)', 'CursoController::delete/$1');
