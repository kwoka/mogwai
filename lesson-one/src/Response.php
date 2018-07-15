<?php
namespace App;

/**
 * Handles everything response-related.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class Response
{
	/**
	 * Output content.
	 * @var null
	 */
	private $body = '';

	/**
	 * Headers to be output.
	 * @var array
	 */
	private $headers = [];

	/**
	 * Http code.
	 * @var integer
	 */
	private $code = 200;

	/**
	 * Returns new instance.
	 * @return self
	 */
	public static function instance()
	{
		return new static();
	}

	/**
	 * Attaches a body to the response.
	 * @param mixed $body
	 * @return $this
	 */
	public function withBody($body)
	{
		$this->body = $body;
		return $this;
	}

	/**
	 * Json encodes the response body and sets the content type header.
	 * @param mixed $body
	 * @return $this
	 */
	public function withJson($body)
	{
		$this->body = json_encode($body);
		$this->withHeader('Content-Type', 'application/json');
		return $this;
	}

	/**
	 * Sets a response header.
	 * @param string $name
	 * @param string $value
	 * @return $this
	 */
	public function withHeader($name, $value)
	{
		$this->headers[$name] = $value;
		return $this;
	}

	/**
	 * Sets https status code.
	 * @param int $code
	 * @return $this
	 */
	public function withStatus($code)
	{
		$this->code = $code;
		return $this;
	}

	/**
	 * Outputs the response.
	 * @return void
	 */
	public function output()
	{
		foreach ($this->headers as $header => $value) {
			header($header . ':' . $value);
		}
		header('HTTP/1.1 ' . $this->code);
		echo $this->body;
	}
}
