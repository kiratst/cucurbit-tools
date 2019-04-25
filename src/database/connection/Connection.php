<?php

namespace Cucurbit\Tools\Database\Connection;

use Cucurbit\Tools\Database\Connection\Interfaces\ConnectionInterface;
use Cucurbit\Tools\Database\Connector\Connector;
use Cucurbit\Tools\Database\Connector\ConnectorInterface;
use PDO;

class Connection implements ConnectionInterface
{
	/**
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * @var string
	 */
	protected $database;

	/**
	 * @var string
	 */
	protected $tablePrefix = '';

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * fetch mode
	 * @var int
	 */
	protected $fetchMode = PDO::FETCH_OBJ;

	/**
	 * Connection constructor.
	 * @param Connector $connector
	 */
	public function __construct($connector)
	{
		$this->pdo         = $connector->pdo;
		$this->database    = $connector->database;
		$this->tablePrefix = $connector->prefix;
	}


	/**
	 * @param string $table
	 * @return Builder|mixed
	 */
	public function table($table)
	{
		return $this->builder()->table($table);
	}

	public function one($query, $bindings = [])
	{

	}

	public function all($query, $bindings = [])
	{

	}

	public function insert($query, $bindings = [])
	{
	}

	public function update($query, $bindings = [])
	{
	}

	public function delete($query, $bindings = [])
	{
	}

	/**
	 * @return Builder
	 */
	public function builder()
	{
		return new Builder($this);
	}

	/**
	 * @return PDO
	 */
	public function getPdo()
	{
		return $this->pdo;
	}
}