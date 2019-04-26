<?php

namespace Cucurbit\Tools\Database\Connection\Traits;

use PDO;
use PDOStatement;

/**
 * Trait Query Trait
 * @package Cucurbit\Tools\Database\Connection\Traits
 */
trait QueryTrait
{

	/**
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * fetch mode
	 * @var int
	 */
	protected $fetchMode = PDO::FETCH_OBJ;

	/**
	 * run sql statement
	 *
	 * @param string   $sql
	 * @param array    $bindings
	 * @param callable $callback
	 * @return mixed
	 */
	protected function execSql($sql, $bindings, $callback)
	{
		try {
			$result = $callback($sql, $bindings);
		} catch (\Throwable $e) {
			throw new \PDOException($e->getMessage());
		}

		return $result;
	}

	/**
	 * run the statement
	 *
	 * @param string $sql
	 * @param array  $bindings
	 * @param bool   $return_statement
	 * @return mixed
	 */
	protected function execStatement($sql, $bindings = [], $return_statement = true)
	{
		/** @var PDOStatement $statement */
		$statement = $this->pdo->prepare($sql);

		$this->setFetchMode($statement);
		$this->bindValues($statement, $bindings);

		$result = $statement->execute();

		return $return_statement ? $statement : $result;
	}

	/**
	 * @param PDOStatement $statement
	 */
	protected function setFetchMode($statement)
	{
		$statement->setFetchMode($this->fetchMode);
	}

	/**
	 * @param PDOStatement $statement
	 * @param array        $bindings
	 */
	protected function bindValues($statement, $bindings = [])
	{
		foreach ($bindings as $key => $value) {
			$statement->bindValue(
				\is_string($key) ? $key : $key + 1,
				$value,
				\is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
			);
		}
	}

	/**
	 * return affected rows count
	 *
	 * @param string $sql
	 * @param array  $bindings
	 * @return int
	 */
	protected function affectedRows($sql, $bindings = [])
	{
		return $this->execSql($sql, $bindings, function ($sql, $bindings) {
			return $this->execStatement($sql, $bindings)->rowCount();
		});
	}

}