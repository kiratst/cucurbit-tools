<?php

namespace Cucurbit\Tools\Database\Traits;

use Closure;
use Cucurbit\Tools\Database\Query\Builder;

/**
 * Trait where conditions
 *
 * @package Cucurbit\Tools\Database\Traits
 */
trait WhereTrait
{
	/**
	 * @var array
	 */
	public $wheres = [];

	/**
	 * 允许的操作符
	 *
	 * @var array
	 */
	public $operators = [
		'=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
		'like', 'like binary', 'not like', 'ilike',
		'&', '|', '^', '<<', '>>',
		'rlike', 'regexp', 'not regexp',
		'~', '~*', '!~', '!~*', 'similar to',
		'not similar to', 'not ilike', '~~*', '!~~*',
	];

	/**
	 * set where conditions
	 *
	 * @param string|array|Closure $column
	 * @param mixed                $operator
	 * @param mixed                $value
	 * @param string               $expression
	 * @return $this
	 */
	public function where($column, $operator = null, $value = null, $expression = 'and')
	{
		if (\is_array($column)) {
			return $this->convertArrayWhere($column, $expression);
		}

		if ($column instanceof Closure) {
			return $this->nestedWhere($column, $expression);
		}

		if (!$this->validOperator($operator)) {
			list($value, $operator) = [$operator, '='];
		}

		if ($value === null) {
			return $this->whereNull($column, $expression);
		}

		if (\is_array($value)) {
			$value = $value[0];
		}

		$this->wheres[] = compact('column', 'operator', 'value', 'expression');
		$this->addBindings($value);

		return $this;
	}

	/**
	 * where or
	 *
	 * @param string|array|Closure $column
	 * @param mixed                $operator
	 * @param mixed                $value
	 * @return $this
	 */
	public function whereOr($column, $operator = null, $value = null)
	{
		return $this->where($column, $operator, $value, $expression = 'or');
	}

	/**
	 * where is null
	 *
	 * @param string $column
	 * @param string $expression
	 * @param bool   $not_null
	 * @return $this
	 */
	public function whereNull($column, $expression = 'and', $not_null = false)
	{
		$type = $not_null ? 'NotNull' : 'Null';

		$this->wheres[] = compact('type', 'column', 'expression');

		return $this;
	}

	/**
	 * where not null
	 *
	 * @param string $column
	 * @param string $expression
	 * @return $this
	 */
	public function whereNotNull($column, $expression = 'and')
	{
		return $this->whereNull($column, $expression, true);
	}

	/**
	 * convert columns and add wheres
	 *
	 * @param array  $columns
	 * @param string $expression
	 * @return $this
	 */
	protected function convertArrayWhere($columns, $expression = 'and')
	{
		foreach ($columns as $column => $value) {
			if (is_numeric($column) && \is_array($value)) {
				$this->where(...array_values($value));
			}
			else {
				$this->where($column, '=', $value, $expression);
			}
		}

		return $this;
	}

	/**
	 * nested where conditions
	 *
	 * @param Closure $callback
	 * @param string  $expression
	 * @return $this
	 */
	protected function nestedWhere(Closure $callback, $expression = 'and')
	{
		\call_user_func($callback, $query = $this->newQuery()->table($this->table));

		return $this->addNestedWhere($query, $expression);
	}

	/**
	 * @param Builder $query
	 * @param string  $expression
	 * @return $this
	 */
	protected function addNestedWhere($query, $expression = 'and')
	{
		if (\count($query->wheres)) {
			$type = 'Nested';

			$this->wheres[] = compact('type', 'query', 'expression');

			$this->addBindings($query->bindings);
		}

		return $this;
	}
}