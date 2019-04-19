<?php

namespace Cucurbit\Tools\Database\Interfaces;

/**
 * Interface QueryInterface
 *
 * @package Cucurbit\Tools\Database\Interfaces
 */
interface QueryInterface
{
	/**
	 * init database table
	 *
	 * @param string $table
	 * @return mixed
	 */
	public function table($table);

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
	public function select($query, $bindings = []);

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