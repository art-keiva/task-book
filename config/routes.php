<?php

return [
	'task' => [
		'path' => '/',
		'controller' => 'App\Controller\TaskController::index',
	],
	'login' => [
		'path' => '/login',
		'controller' => 'App\Controller\UserController::index',
	],
	'logout' => [
		'path' => '/logout',
		'controller' => 'App\Controller\UserController::logout',
	],
	'status' => [
		'path' => '/status',
		'controller' => 'App\Controller\TaskController::setStatus',
	],
	'edit' => [
		'path' => '/edit',
		'controller' => 'App\Controller\TaskController::editDescription',
	],
];