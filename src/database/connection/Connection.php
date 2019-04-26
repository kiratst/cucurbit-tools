<?php

namespace Cucurbit\Tools\Database\Connection;

use Cucurbit\Tools\Database\Connector\Connector;
use Cucurbit\Tools\Database\Traits\QueryTrait;
use PDO;

class Connection implements ConnectionInterface
{
	use QueryTrait;

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
	 * run statement and get one result
	 *
	 * @param string $sql
	 * @param array  $bindings
	 * @return mixed
	 */
	public function one($sql, $bindings = [])
	{
		$result = $this->all($sql, $bindings);

		return array_shift($result);
	}

	/**
	 * run statement and get result
	 *
	 * @param string $sql
	 * @param array  $bindings
	 * @return mixed
	 */
	public function all($sql, $bindings = [])
	{
		return $this->execSql($sql, $bindings, function ($sql, $bindings) {
			return $this->execStatement($sql, $bindings)
				->fetchAll();
		});

	}

	/**
	 * run insert sql
	 *
	 * @param string $sql
	 * @param array  $bindings
	 * @return mixed|void
	 */
	public function insert($sql, $bindings = [])
	{
		return $this->execSql($sql, $bindings, function ($sql, $bindings) {
			return $this->execStatement($sql, $bindings, false);
		});
	}

	/**
	 * run update sql and return the affected rows count
	 *
	 * @param string $sql
	 * @param array  $bindings
	 * @return int|mixed
	 */
	public function update($sql, $bindings = [])
	{
		return $this->affectedRows($sql, $bindings);
	}

	/**
	 * run delete sql and return the affected rows count
	 *
	 * @param string $sql
	 * @param array  $bindings
	 * @return int|mixed
	 */
	public function delete($sql, $bindings = [])
	{
		return $this->affectedRows($sql, $bindings);
	}

}