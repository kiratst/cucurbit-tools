<?php

namespace Cucurbit\Tools\Database\Connection\Traits;

use PDO;
use PDOStatement;

trait QueryTrait
{

	/**
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * run sql statement
	 * @param $query
	 * @param $bindings
	 */
	protected function run($query, $bindings)
	{
		/** @var PDOStatement $statement */
		$statement = $this->pdo->prepare($query);

		$this->bindValues($statement, $bindings);

		$statement->execute();
	}

	/**
	 * @param PDOStatement $statement
	 * @param              $bindings
	 */
	protected function bindValues($statement, $bindings)
	{

		foreach ($bindings as $key => $value) {
			$statement->bindValue(
				\is_string($key) ? $key : $key + 1,
				$value,
				\is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
			);
		}
	}

}