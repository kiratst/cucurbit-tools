<?php

namespace Cucurbit\Tools\Database\Builder;

use Cucurbit\Tools\Support\Arr;
use Cucurbit\Tools\Database\Connector\Connector;
use Cucurbit\Tools\Database\Dao\Dao;
use Cucurbit\Tools\Database\Resolver\Resolver;
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
	 * @var Connector
	 */
	public $connector;

	/**
	 * @var Resolver
	 */
	public $resolver;

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
		$this->dao       = $dao;
		$this->connector = $this->connector ?: $this->getConnector();
		$this->resolver  = $this->resolver ?: $this->getResolver();
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
	 * @return $this
	 */
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

	/**
	 * @return $this
	 */
	public function groupBy()
	{
		$groups = \func_get_args();

		foreach ($groups as $group) {
			$group = \is_array($group) ? $group : [$group];

			$this->groups = array_merge((array) $this->groups, $group);
		}

		return $this;
	}

	/**
	 * @param string $column
	 * @param string $direction
	 * @return $this
	 */
	public function orderBy($column, $direction = 'asc')
	{
		$this->orders[] = [
			'column'    => $column,
			'direction' => strtolower($direction) === 'asc' ? 'asc' : 'desc',
		];

		return $this;
	}

	/**
	 * @param $column
	 * @return Builder
	 */
	public function orderByDesc($column)
	{
		return $this->orderBy($column, 'desc');
	}

	/**
	 * @param array $columns
	 * @return mixed
	 */
	public function get($columns = ['*'])
	{
		$this->columns = !$this->columns ? $columns : $this->columns;

		return $this->dao->all($this->toSql(), $this->getBindings());
	}

	/**
	 * @param array $data
	 * @return mixed|void
	 */
	public function insert(array $data)
	{
		return $this->dao->insert($this->resolver->resolveInsert($this, $data),
			$this->resolver->prepareBindings($data, $this));
	}

	/**
	 * @param array $data
	 * @return int|mixed
	 */
	public function update(array $data)
	{
		return $this->dao->update($this->resolver->resolveUpdate($this, $data),
			$this->resolver->prepareBindings($data, $this)
		);
	}

	/**
	 * @param null $id
	 * @return int|mixed
	 */
	public function delete($id = null)
	{
		if ($id) {
			$this->where('id', '=', $id);
		}

		return $this->dao->delete($this->resolver->resolveDelete($this), $this->getBindings());
	}

	/**
	 * @param $limit
	 * @return $this
	 */
	public function limit($limit)
	{
		$this->limit = $limit <= 0 ? 1 : (int) $limit;

		return $this;
	}

	/**
	 * get one record by id
	 *
	 * @param mixed $id
	 * @param array $columns
	 * @return mixed
	 */
	public function find($id, $columns = ['*'])
	{
		return $this->where('id', '=', $id)->first($columns);
	}

	/**
	 * get the first result
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

	/**
	 * resolve builder to sql string
	 *
	 * @return mixed|string
	 */
	protected function toSql()
	{
		return $this->resolver->toSql($this);
	}

	/**
	 * @return Connector
	 */
	protected function getConnector()
	{
		return $this->dao->getConnector();
	}

	/**
	 * @return Resolver
	 */
	protected function getResolver()
	{
		return $this->getConnector()->getResolver();
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

	public function getBindings()
	{
		return Arr::flatten($this->bindings);
	}
}