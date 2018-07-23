<?php
namespace App;

/**
 * Handles all DB related functionalities.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class DB
{
	/**
	 * Database connection.
	 * @var PDO
	 */
	protected $conn = null;

	/**
	 * Instantiate a new instance.
	 * @param array $config connection parameters
	 */
	private function __construct($config)
	{
		$this->conn = new \PDO("mysql:host={$config['host']};dbname={$config['database']}", $config['user'], $config['pass']);
	}

	/**
	 * Executes a prepared query and returns the result set.
	 * @param string $query query to be executed
	 * @param array $params (optional) query parameters
	 * @param array $options (optional) query options
	 * @return Object PDOprepared statement post execution
	 * @throws \PDOException
	 */
	public function query($query, array $params = [], array $options = [])
	{
		$statement = $this->conn->prepare($query);
		$statement->execute($params);
		return $statement;
	}

	/**
	 * Return the last insert id from the last performed query.
	 * @return int
	 */
	public function lastInsertId()
	{
		return $this->conn->lastInsertId();
	}
}
