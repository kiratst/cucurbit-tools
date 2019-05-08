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

	public function toSql(Builder $builder)
	{
		return $this->concatenateSql($this->resolveComponents($builder));
	}

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

	protected function concatenateColumn($columns)
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

	protected function concatenateSql($sql)
	{
		return implode(' ', array_filter($sql, function ($value) {
			return (string) $value !== '';
		}));
	}
}