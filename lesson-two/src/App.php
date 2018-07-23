<?php
namespace App;

/**
 * Application container.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class App
{
	/**
	 * Application container with attached dependencies.
	 * @var array
	 */
	private $container = [];

	/**
	 * Instantiate a new instance.
	 * @param array $settings (optional) array of app settings
	 */
	public function __construct(array $settings = [])
	{
		$this->router = new Router();
		// apply the settings to the container
		$this->container['settings'] = $settings;
	}

	/**
	 * Returns the app container.
	 * @return array
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * Adds a route to the set of route rules.
	 * @param string|array $method accepted methods
	 * @param string $route
	 * @param Callable $callable function to be executed
	 * @return void
	 * @throws \Exception invalid callable is provided
	 */
	public function route($method, $route, $callable)
	{
		$this->router->route($method, $route, $callable);
	}

	/**
	 * Executes the app.
	 * @return void
	 * @throws \Exception
	 */
	public function run()
	{
		$this->router->run();
	}
}
