<?php

namespace Cucurbit\Tools\Database\Resolver;

use Cucurbit\Tools\Database\Builder\Builder;

class MysqlResolver extends Resolver
{

	/**
	 * resolve table name
	 *
	 * @param Builder $builder
	 * @param string  $table
	 * @return string
	 */
	protected function resolveTable($builder, $table)
	{
		return 'from ' . $this->tablePrefix . $table;
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