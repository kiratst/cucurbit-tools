<?php

namespace Cucurbit\Tools\Database\Builder;

use Cucurbit\Tools\Collection\Arr;
use Cucurbit\Tools\Database\Dao\Dao;
use Cucurbit\Tools\Database\Traits\WhereTrait;
use InvalidArgumentException;

/**
 * Builder the sql
 */
class Builder implements BuilderInterface
{
	use WhereTrait;

	/**
	 * @var Dao
	 */
	public $dao;

	/**
	 * @var string
	 */
	public $table;

	/**
	 * @var bool
	 */
	public $distinct;

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
	 * the select columns
	 *
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
	 * @param $dao
	 */
	public function __construct($dao)
	{
		$this->dao = $dao;
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

	public function distinct()
	{
		$this->distinct = true;

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

	public function groupBy()
	{
		$groups = \func_get_args();

		foreach ($groups as $group) {
			$group = \is_array($group) ? $group : [$group];

			$this->groups = array_merge((array) $this->groups, $group);
		}

		return $this;
	}

	public function orderBy($column, $direction = 'asc')
	{
		$this->orders[] = [
			'column'    => $column,
			'direction' => strtolower($direction) === 'asc' ? 'asc' : 'desc',
		];

		return $this;
	}

	public function orderByDesc($column)
	{
		return $this->orderBy($column, 'desc');
	}

	public function get($columns = ['*'])
	{
		$this->columns = !$this->columns ? $columns : $this->columns;

		return $this->runSql();
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

	public function limit($limit)
	{
		$this->limit = $limit <= 0 ? 1 : $limit;

		return $this;
	}

	/**
	 * get one record by id
	 *
	 * @param       $id
	 * @param array $columns
	 * @return mixed
	 */
	public function find($id, $columns = ['*'])
	{
		return $this->where('id', '=', $id)->first($columns);
	}

	/**
	 * get first the result
	 *
	 * @param array $columns
	 * @return mixed
	 */
	public function first($columns = ['*'])
	{
		$this->columns = !$this->columns ? $columns : $this->columns;

		return $this->dao->one($this->limit(1)->toSql(), $this->getBindings());
	}

	/**
	 * get column value
	 *
	 * @param $column
	 * @return mixed|null
	 */
	public function value($column)
	{
		$data = (array) $this->first([$column]);

		return \count($data) ? reset($data) : null;
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
		return $this->dao->getConnector()->getResolver()->toSql($this);
	}

	public function runSql()
	{
		return $this->dao->all($this->toSql(), $this->getBindings());
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
		return new static($this->dao);
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
		if (!\array_key_exists($type, $this->bindings)) {
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

	protected function getBindings()
	{
		return Arr::flatten($this->bindings);
	}
}