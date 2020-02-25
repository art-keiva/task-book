<?php

namespace App\Kernel;

use Symfony\Component\HttpFoundation\Request;

class Router
{
	public $request;
	
	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	public function run()
	{
		$this->controllerDeterminant();
	}

	public function getConfigRoutes()
	{
		return require_once($_ENV['APP_DIR'] . '/config/routes.php');
	}

	private function controllerDeterminant()
	{
		$routes = $this->getConfigRoutes();
		$detectedRoute = null;

		foreach ($routes as $routeName => $routeParams) {
			if ($routeParams['path'] === $this->request->getPathInfo()) {
				$detectedRoute = $routeName;
			}
		}

		if (null === $detectedRoute) {
			echo '404 - PAGE NOT FOUND';
			die;
		}

		$controllerName = strstr($routes[$detectedRoute]['controller'], '::', true);
		$method = str_replace('::', '', strstr($routes[$detectedRoute]['controller'], '::', false));

		if (!class_exists($controllerName)) {
			throw new \Exception("Class $controllerName does not exists");
		}

		if (!method_exists($controllerName, $method)) {
			throw new \Exception("Method $method in $controllerName does not exists");
		}

		$controller = new $controllerName($this->request, $controllerName);

		return $controller->{$method}();
	}
}
