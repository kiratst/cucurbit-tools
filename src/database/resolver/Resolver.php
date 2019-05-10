<?php

namespace Cucurbit\Tools\Database\Resolver;

use Cucurbit\Tools\Database\Builder\Builder;

/**
 * resolve builder to sql
 */
abstract class Resolver implements ResolverInterface
{

	protected $tablePrefix = '';

	protected $components = [
		'columns',
		'table',
		'wheres',
		'groups',
		'orders',
		'offset',
		'limit',
	];

	public function setTablePrefix($prefix = '')
	{
		$this->tablePrefix = $prefix;

		return $this;
	}

	/**
	 * resolve builder to sql string
	 *
	 * @param Builder $builder
	 * @return mixed|string
	 */
	public function toSql(Builder $builder)
	{
		return $this->concatenateSql($this->resolveComponents($builder));
	}

	/**
	 * resolve delete sql
	 *
	 * @param Builder $builder
	 */
	public function resolveDelete(Builder $builder)
	{

	}

	/**
	 * resolve update
	 *
	 * @param Builder $builder
	 * @param array   $data
	 */
	public function resolveUpdate(Builder $builder, array $data)
	{

	}

	/**
	 * @param Builder $builder
	 * @param array   $data
	 */
	public function resolveInsert(Builder $builder, array $data)
	{

	}

	/**
	 * @param array   $data
	 * @param Builder $builder
	 * @return array
	 */
	public function prepareBindings(array $data, Builder $builder)
	{
		return array_values(array_merge($data, $builder->getBindings()));
	}

	/**
	 * resolve the list components
	 *
	 * @param Builder $builder
	 * @return array
	 */
	protected function resolveComponents(Builder $builder)
	{
		$sql = [];
		if ($builder->columns === null) {
			$builder->columns = ['*'];
		}

		foreach ($this->components as $component) {
			if ($builder->$component === null) {
				continue;
			}

			$method = 'resolve' . ucfirst($component);
			if (method_exists($this, $method)) {
				$sql[$component] = $this->$method($builder, $builder->$component);
			}
		}

		return $sql;
	}

	/**
	 * @param array $columns
	 * @return string
	 */
	protected function concatenateColumn(array $columns)
	{
		return implode(', ', $columns);
	}

	/**
	 * @param array $wheres
	 * @return array
	 */
	protected function convertWheresToArray($wheres)
	{
		$result = [];
		foreach ($wheres as $where) {
			$result[] = $where['expression'] . ' ' . $where['column'] . ' ' . $where['operator'] . ' ?';
		}

		return $result;
	}

	/**
	 * @param $sql
	 * @return string
	 */
	protected function concatenateSql($sql)
	{
		return implode(' ', array_filter($sql, function ($value) {
			return (string) $value !== '';
		}));
	}

	/**
	 * @param Builder $builder
	 * @return string
	 */
	protected function getTable(Builder $builder)
	{
		return $this->tablePrefix . $builder->table;
	}
}