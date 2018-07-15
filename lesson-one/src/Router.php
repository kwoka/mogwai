<?php
namespace App;

use App\Request;
use App\Response;

/**
 * Handles routing incoming requests and associated route rules.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class Router
{
	/**
	 * Define accepted http methods.
	 * @var array
	 */
	protected static $supportedMethods = [
		'OPTIONS', 'HEAD', 'POST', 'GET', 'PUT', 'DELETE', 'PATHCH'
	];

	/**
	 * Defined routes and their actions.
	 * @var array
	 */
	protected static $routes = [];

	/**
	 * Adds a route to the set of route rules.
	 * @param string|array $method accepted methods
	 * @param string $route
	 * @param Callable $callable function to be executed
	 * @return void
	 * @throws \Exception invalid callable or invalid method provided
	 */
	public static function route($method, $route, $callable)
	{
		// ensure the endpoint is callable
		if (!is_callable($callable)) {
			throw new \Exception('The provided callable is not callable when defining route: ' . $route);
		}
		// validate the methods
		$methods = is_array($method) ? $method : [$method];
		$methods = array_map('strtoupper', $methods);
		foreach ($methods as $method) {
			if (!in_array($method, self::$supportedMethods)) {
				throw new \Exception('Invalid method: ' . $method);
			}
		}
		self::$routes[$route] = [
			'methods' => $methods,
			'callable' => $callable
			];
	}

	/**
	 * Matches the defined route and executes the callable.
	 * @return void
	 * @throws \Exception route not matched
	 */
	public static function run()
	{
		// remove any rewrite base
		$requestUri = str_ireplace($_SERVER['BASE'], '', $_SERVER['REQUEST_URI']);
		foreach (self::$routes as $path => $data) {
			if ($path == $requestUri && in_array(strtoupper($_SERVER['REQUEST_METHOD']), $data['methods'])) {
				call_user_func($data['callable'], Request::getInstance(), Response::instance());
				return;
			}
		}
		throw new \Exception('Resource not found.');
	}
}
