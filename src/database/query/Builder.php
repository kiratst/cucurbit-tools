<?php

namespace Cucurbit\Tools\Database\Query;

use Closure;
use InvalidArgumentException;

/**
 * Builder the sql
 */
class Builder
{
	public $connection;

	/**
	 * @var string
	 */
	public $table;

	/**
	 * where conditions
	 *
	 * @var array
	 */
	public $wheres = [];

	/**
	 * @var array
	 */
	public $groups;

	/**
	 * @var array
	 */
	public $orders;

	/**
	 * @var int
	 */
	public $offset;

	/**
	 * @var int
	 */
	public $limit;

	/**
	 * @var array
	 */
	public $columns;

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
	 * @var array
	 */
	public $bindings = [
		'where' => [],
	];

	/**
	 * Builder constructor.
	 * @param $connection
	 */
	public function __construct($connection)
	{
		$this->connection = $connection;
	}

	/**
	 * set the selected columns
	 *
	 * @param array $columns
	 * @return $this
	 */
	public function select($columns = ['*'])
	{
		$this->columns = \is_array($columns) ? $columns : \func_get_args();

		return $this;
	}

	/**
	 * set the table
	 *
	 * @param string $table
	 * @return $this
	 */
	public function table($table)
	{
		$this->table = $table;

		return $this;
	}

	/**
	 * set where conditions
	 *
	 * @param string|array $column
	 * @param mixed        $operator
	 * @param mixed        $value
	 * @param string       $expression
	 * @return Builder
	 */
	public function where($column, $operator = null, $value = null, $expression = 'and')
	{
		if (\is_array($column)) {
			return $this->convertArrayWhere($column, $expression);
		}

		if ($column instanceof Closure) {
			return;
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
	 * @return Builder
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
	 * @return Builder
	 */
	public function whereNotNull($column, $expression = 'and')
	{
		return $this->whereNull($column, $expression, true);
	}

	public function groupBy()
	{

	}

	public function orderBy()
	{

	}

	public function get()
	{

	}

	public function insert()
	{

	}

	public function update()
	{

	}

	public function delete()
	{

	}

	public function find($id)
	{

	}

	public function first()
	{

	}

	public function sum()
	{

	}

	public function max()
	{

	}

	public function min()
	{

	}

	public function toSql()
	{

	}

	public function runSql()
	{

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
	 * valid operator
	 *
	 * @param $operator
	 * @return bool
	 */
	protected function validOperator($operator)
	{
		return \in_array(strtolower($operator), $this->operators, false);
	}

	/**
	 * @return Builder
	 */
	protected function newQuery()
	{
		return new static($this->connection);
	}

	/**
	 * add bindings
	 *
	 * @param mixed  $value
	 * @param string $type
	 * @return $this
	 */
	protected function addBindings($value, $type = 'where')
	{
		if (!array_key_exists($type, $this->bindings)) {
			throw new InvalidArgumentException("Invalid binding type: {$type}.");
		}

		if (\is_array($value)) {
			$this->bindings[$type][] = array_values(array_merge($this->bindings[$type], $value));
		}
		else {
			$this->bindings[$type][] = $value;
		}

		return $this;
	}
}