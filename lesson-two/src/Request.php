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
	 * Contains request headers.
	 * @var array 
	 */
	private $headers = [];

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
		if (empty($this->headers)) {
			foreach ($_SERVER as $key => $value) {
				if (substr($key, 0, 5) <> 'HTTP_') {
		            continue;
		        }
		        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $this->headers[$header] = $value;
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

	/**
	 * Returns all request headers.
	 * @return array
	 */
	public function getRequestHeaders()
	{
		return $this->headers;
	}

	/**
	 * Returns a request header by name.
	 * @param string $name name of the header
	 * @return string|null
	 */
	public function getRequestHeader($name)
	{
		if (isset($this->headers[$name])) {
			return $this->headers[$name];
		}
		return null;
	}
}
