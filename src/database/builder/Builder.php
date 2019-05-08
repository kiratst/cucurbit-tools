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

	}

	public function orderBy()
	{

	}

	public function get($columns = ['*'])
	{
		$origin = $this->columns;

		if ($origin === null) {
			$this->columns = $origin;
		}

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
		\DB::table('mk_account_front')->where('account_id', '>=', 12)
			->where('nickname', '=', '123')->limit(1)->get();

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