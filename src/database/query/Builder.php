<?php

namespace Cucurbit\Tools\Database\Query;

/**
 * builder the sql
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
	 * groups
	 *
	 * @var array
	 */
	public $groups;
	/**
	 * the orders
	 *
	 * @var array
	 */
	public $orders;
	/**
	 * the number of offset
	 *
	 * @var int
	 */
	public $offset;
	/**
	 * the number of return record
	 * @var int
	 */
	public $limit;
	/**
	 * the selected columns
	 *
	 * @var array
	 */
	public $columns;
	/**
	 * valid operators
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
	 * @param string $table
	 * @return $this
	 */
	public function table($table)
	{
		$this->table = $table;
		return $this;
	}
	/**
	 * combine where conditions
	 *
	 * e.g.
	 * 'name', '=', 'aaa',
	 * 'name', 'aaa'
	 * [
	 *      'name' => 'aaa',
	 *      'age'  => 12
	 * ]
	 *
	 *
	 * @param        $columns
	 * @param null   $operator
	 * @param null   $value
	 * @param string $link
	 * @return $this
	 */
	public function where($columns, $operator = null, $value = null, $link = 'and')
	{
		if (\is_array($columns)) {
			return $this->convertWhere($columns);
		}
		if (!$this->validOperator($operator)) {
			list($value, $operator) = [$operator, '='];
		}
		$this->wheres[] = compact('columns', 'operator', 'value', 'link');
		$this->addWhere($value);
		return $this;
	}
	/**
	 * convert and nested where
	 * @param        $columns
	 * @param string $link
	 * @return $this
	 */
	protected function convertWhere($columns, $link = 'and')
	{
		$query = $this->newQuery();
		foreach ($columns as $key => $value) {
			if (is_numeric($key) && \is_array($value)) {
				$query->where(array_values($value));
			}
			else {
				$query->where($key, '=', $value, $link);
			}
		}
		return $this->nestedWhere($query, $link);
	}
	/**
	 * @param static $query
	 * @param string $link
	 * @return $this
	 */
	protected function nestedWhere($query, $link = 'and')
	{
		if (\count($query->wheres)) {
			$this->addWhere($query->wheres);
		}
		return $this;
	}
	/**
	 * add wheres to property
	 *
	 * @param string|array $values
	 * @return $this
	 */
	protected function addWhere($values)
	{
		if (\is_array($values)) {
			$this->wheres = array_values(array_merge($this->wheres, $values));
		} else {
			$this->wheres[] = $values;
		}
		return $this;
	}
	/**
	 * @param string $operator
	 * @return bool
	 */
	protected function validOperator($operator): bool
	{
		return \in_array(strtolower($operator), $this->operators, true);
	}
	protected function newQuery()
	{
		return new static($this->connection);
	}
	/**
	 * @return array
	 */
	protected function getWheres()
	{
		return $this->wheres;
	}
}