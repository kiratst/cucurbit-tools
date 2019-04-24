<?php

namespace Cucurbit\Tools\Database\Connection;

use Cucurbit\Tools\Database\Connection\Interfaces\ConnectionInterface;
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
	protected $table_prefix = '';

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * fetch mode
	 * @var int
	 */
	protected $fetch_mode = PDO::FETCH_OBJ;

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
}