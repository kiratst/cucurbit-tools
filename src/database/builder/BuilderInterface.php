<?php

namespace Cucurbit\Tools\Database\Builder;

/**
 * interface builder
 * @package Cucurbit\Tools\Database\Builder
 */
interface BuilderInterface
{
	/**
	 * @param string $table
	 * @return mixed
	 */
	public function table($table);

	/**
	 * @param array $columns
	 * @return mixed
	 */
	public function select($columns = ['*']);

	/**
	 * @param mixed $columns
	 * @param null  $operator
	 * @param null  $value
	 * @return mixed
	 */
	public function where($columns, $operator = null, $value = null);

	/**
	 * @param array $columns
	 * @return mixed
	 */
	public function get($columns = ['*']);

}