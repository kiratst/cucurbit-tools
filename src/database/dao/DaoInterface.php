<?php

namespace Cucurbit\Tools\Database\Dao;

/**
 * Interface ConnectionInterface
 *
 * @package Cucurbit\Tools\Database\Interfaces
 */
interface DaoInterface
{
	/**
	 * run statement and return one result
	 *
	 * @param string $query
	 * @param array  $bindings
	 * @return mixed
	 */
	public function one($query, $bindings = []);

	/**
	 * run statement and return result
	 *
	 * @param string $query
	 * @param array  $bindings
	 * @return mixed
	 */
	public function all($query, $bindings = []);

	/**
	 * insert data
	 *
	 * @param string $query
	 * @param array  $bindings
	 * @return mixed
	 */
	public function insert($query, $bindings = []);

	/**
	 * update data
	 *
	 * @param string $query
	 * @param array  $bindings
	 * @return mixed
	 */
	public function update($query, $bindings = []);

	/**
	 * delete data
	 *
	 * @param string $query
	 * @param array  $bindings
	 * @return mixed
	 */
	public function delete($query, $bindings = []);
}