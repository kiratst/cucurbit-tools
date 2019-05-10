<?php

namespace Cucurbit\Tools\Database\Resolver;

use Cucurbit\Tools\Database\Builder\Builder;

class MysqlResolver extends Resolver
{

	/**
	 * @param Builder $builder
	 * @return string
	 */
	public function resolveDelete(Builder $builder)
	{
		$wheres = \is_array($builder->wheres) ? $this->resolveWheres($builder, $builder->wheres) : '';

		return 'delete from ' . $this->getTable($builder) . ' ' . $wheres;
	}

	/**
	 * @param Builder $builder
	 * @param array   $data
	 * @return string
	 */
	public function resolveUpdate(Builder $builder, array $data)
	{
		$table = $this->getTable($builder);

		$columns = [];
		foreach ($data as $key => $value) {
			$columns[] = $key . '= ? ';
		}

		$columns = implode(', ', $columns);
		$wheres  = $this->resolveWheres($builder, $builder->wheres);

		return 'update ' . $table . ' set ' . $columns . ' ' . $wheres;
	}

	/**
	 * @param Builder $builder
	 * @param array   $data
	 * @return string
	 */
	public function resolveInsert(Builder $builder, array $data)
	{

		$table = $this->getTable($builder);

		$columns = array_keys($data);
		$values  = array_values($data);

		$columns = $this->concatenateColumn($columns);

		$values = array_map(function ($item) {
			return '?';
		}, $values);

		return 'insert into ' . $table . ' (' . $columns . ') values (' . $this->concatenateColumn($values) . ');';
	}

	/**
	 * resolve table name
	 *
	 * @param Builder $builder
	 * @param string  $table
	 * @return string
	 */
	protected function resolveTable($builder, $table)
	{
		return 'from ' . $this->tablePrefix . $builder->table;
	}

	/**
	 * @param Builder $builder
	 * @param array   $columns
	 * @return mixed
	 */
	protected function resolveColumns($builder, $columns)
	{

		$sql = $builder->distinct ? 'select distinct ' : 'select ';

		return $sql . $this->concatenateColumn($columns);
	}

	/**
	 * @param Builder $builder
	 * @param array   $wheres
	 * @return string
	 */
	protected function resolveWheres($builder, $wheres)
	{
		if (\count($where = $this->convertWheresToArray($wheres)) > 0) {
			$where = implode(' ', $where);
			return 'where ' . preg_replace('/and |or /i', '', $where, 1);
		}

		return '';
	}

	/**
	 * @param Builder $builder
	 * @param array   $orders
	 * @return string
	 */
	protected function resolveOrders($builder, $orders)
	{
		if (!$orders) {
			return '';
		}

		$order_sql = array_map(function ($order) {
			return $order['column'] . ' ' . $order['direction'];
		}, $orders);

		return 'order by ' . implode(', ', $order_sql);
	}

	/**
	 * @param Builder $builder
	 * @param array   $groups
	 * @return string
	 */
	protected function resolveGroups($builder, $groups)
	{
		return 'group by ' . $this->concatenateColumn($groups);
	}


}