<?php
namespace App;

/**
 * Handles all request-related functionalities.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class Request
{
	/**
	 * Contains raw input.
	 * @var array
	 */
	private $rawData = [];

	/**
	 * Singleton
	 * @var Self
	 */
	private static $instance = null;

	/**
	 * Returns instance.
	 * @return Self
	 */
	public function getInstance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Instantiate a new instance.
	 */
	private function __construct()
	{
		if (empty($this->rawData) && !empty($rawBody = file_get_contents("php://input"))) {
			// assume it's just json
			if ($postJson = json_decode($rawBody, true)) {
				$this->rawData = $postJson;
			}
		}	
	}

	/**
	 * Returns a get parameter by name.
	 * @param string $paramName
	 * @return mixed|null null when not found
	 */
	public function get($paramName)
	{
		if (isset($_GET[$paramsName])) {
			return $_GET[$paramName];
		}
		return null;
	}

	/**
	 * Returns a post parameter by name.
	 * @param string $paramName
	 * @return mixed|null null when not found
	 */
	public function post($paramName)
	{
		if (isset($this->rawData[$paramName])) {
			return $this->$rawData[$paramName];
		} else if (isset($_POST[$paramsName])) {
			return $_POST[$paramName];
		}
		return null;
	}

	/**
	 * Returns the post body as an array.
	 * @return array
	 */
	public function getParsedBody()
	{
		if (!empty($this->rawData)) {
			return $this->rawData;
		}
		return $_POST;
	}
}
