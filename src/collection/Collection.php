<?php

namespace Cucurbit\Tools\Collection;

use ArrayAccess;
use Cucurbit\Tools\Common\Interfaces\Arrayable;
use Cucurbit\Tools\Common\Traits\ArrayAttributeTrait;

/**
 * Class Collection
 */
class Collection implements ArrayAccess, Arrayable
{
	use ArrayAttributeTrait;

	/**
	 * @var array
	 */
	public $data = [];

	/**
	 * Collection constructor.
	 * @param $data
	 */
	public function __construct($data = [])
	{
		$this->data = $this->convertToArray($data);
	}

	/**
	 * create collection
	 * @param array $data
	 * @return Collection
	 */
	public static function make($data = [])
	{
		return new self($data);
	}

	/**
	 * convert data to array
	 *
	 * @param mixed $data
	 * @return array
	 */
	protected function convertToArray($data)
	{
		if (\is_array($data)) {
			return $data;
		}

		if ($data instanceof self) {
			return $data->all();
		}

		if ($data instanceof Arrayable) {
			return $data->toArray();
		}

		return (array) $data;
	}

	/**
	 * get the first item
	 *
	 * @return mixed
	 */
	public function first()
	{
		return Arr::first($this->data);
	}

	/**
	 * get the last item
	 *
	 * @return mixed
	 */
	public function last()
	{
		return Arr::last($this->data);
	}

	/**
	 * get all
	 * @return array
	 */
	public function all()
	{
		return $this->data;
	}

	/**
	 * retrieve exists
	 *
	 * @param string $key
	 * @return bool
	 */
	public function has($key)
	{
		return $this->offsetExists($key);
	}

	/**
	 * get an item by key
	 *
	 * @param mixed $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		if ($this->offsetExists($key)) {
			return $this->data[$key];
		}

		return $default;
	}


	/**
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty($this->data);
	}

	/**
	 * @return bool
	 */
	public function isNotEmpty()
	{
		return !$this->isEmpty();
	}

	/**
	 * get all keys
	 * @return Collection
	 */
	public function keys()
	{
		return new self(array_keys($this->data));
	}

	/**
	 * get all values
	 * @return Collection
	 */
	public function values()
	{
		return new self(array_values($this->data));
	}

	/**
	 * count the number of data
	 *
	 * @return int
	 */
	public function count()
	{
		return \count($this->data);
	}

	/**
	 * get and remove the first item
	 *
	 * @return mixed
	 */
	public function shift()
	{
		return array_shift($this->data);
	}

	/**
	 * get and remove the last item
	 *
	 * @return mixed
	 */
	public function pop()
	{
		return array_pop($this->data);
	}

	/**
	 * reverse items
	 *
	 * @return Collection
	 */
	public function reverse()
	{
		return new self(array_reverse($this->data, true));
	}

	/**
	 * put an item
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * @return $this
	 */
	public function put($key, $value)
	{
		$this->offsetSet($key, $value);

		return $this;
	}

	/**
	 * push an item
	 *
	 * @param mixed $value
	 * @return $this
	 */
	public function push($value)
	{
		$this->offsetSet(null, $value);

		return $this;
	}

	/**
	 * flip the data
	 *
	 * @return Collection
	 * @throws \Exception
	 */
	public function flip()
	{
		try {
			return new self(array_flip($this->data));
		} catch (\Throwable $e) {
			throw new \Exception($e->getMessage());
		}
	}

	/**
	 * diff data
	 *
	 * @param mixed $data
	 * @return Collection
	 */
	public function diff($data)
	{
		return new self(array_diff($this->data, $this->convertToArray($data)));
	}

	/**
	 * get array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array_map(function ($value) {
			return $value instanceof Arrayable ? $value->toArray() : $value;
		}, $this->data);
	}

	/**
	 * return the values from a single column
	 *
	 * @param mixed $column
	 * @param null  $index_key
	 * @return Collection
	 */
	public function column($column, $index_key = null)
	{
		return new self(array_column($this->data, $column, $index_key));
	}

	/**
	 * merge data with given items
	 * @param $data
	 * @return Collection
	 */
	public function merge($data)
	{
		return new self(array_merge($this->data, $this->convertToArray($data)));
	}

	/**
	 * random
	 *
	 * @param null $number
	 * @return array|Collection|mixed
	 */
	public function random($number = null)
	{
		if (!$number) {
			return Arr::random($this->data);
		}

		return new self(Arr::random($this->data, $number));
	}

	/**
	 * shuffle the items
	 *
	 * @return $this
	 */
	public function shuffle()
	{
		shuffle($this->data);

		return $this;
	}

	/**
	 * filter the data
	 *
	 * @param callable|null $callback
	 * @return Collection
	 */
	public function filter(callable $callback = null)
	{
		if ($callback) {
			return new self(Arr::where($this->data, $callback));
		}

		return new self(array_filter($this->data));
	}

	/**
	 * filter items by key value pairs
	 *
	 * @param string     $key
	 * @param string     $operator
	 * @param null|mixed $value
	 * @return Collection
	 */
	public function where($key, $operator, $value = null)
	{
		return $this->filter($this->convertForWhere(...\func_get_args()));
	}

	/**
	 * filter items by key value pairs
	 *
	 * @param string $key
	 * @param mixed  $values
	 * @param bool   $strict
	 * @return Collection
	 */
	public function whereIn($key, $values, $strict = false)
	{
		$values = $this->convertToArray($values);

		return $this->filter(function ($item) use ($key, $values, $strict) {
			return \in_array(Arr::get($item, $key), $values, $strict);
		});
	}

	/**
	 * group array by a field or callback
	 *
	 * @param callback|string $group_by
	 * @return Collection
	 */
	public function groupBy($group_by)
	{
		$group_by = $this->retrieveCallback($group_by);

		$results = [];
		foreach ($this->data as $key => $value) {
			$group_keys = $group_by($value, $key);

			if (!\is_array($group_keys)) {
				$group_keys = [$group_keys];
			}

			foreach ($group_keys as $group_key) {
				$group_key = \is_bool($group_key) ? (int) $group_key : $group_key;

				if (!array_key_exists($group_key, $results)) {
					$results[$group_key] = new self;
				}

				$results[$group_key]->offsetSet(null, $value);
			}
		}

		return new self($results);
	}

	/**
	 * group array by a field or callback
	 *
	 * @param $key_by
	 * @return Collection
	 */
	public function keyBy($key_by)
	{
		$key_by = $this->retrieveCallback($key_by);

		$result = [];

		foreach ($this->data as $key => $value) {
			$group_key = $key_by($value, $key);

			$result[$group_key] = $value;
		}

		return new self($result);
	}

	/**
	 * @param callable $callback
	 * @return Collection
	 */
	public function map(callable $callback)
	{
		$keys = array_keys($this->data);
		$items = array_map($callback, $this->data);

		return new self(array_combine($keys, $items));
	}

	/**
	 * @param callable $callback
	 * @return $this
	 */
	public function each(callable $callback)
	{
		foreach ($this->data as $key => $value) {
			if ($callback($value, $key) === false) {
				break;
			}
		}

		return $this;
	}

	/**
	 * get value retrieve callback
	 *
	 * @param $value
	 * @return \Closure
	 */
	protected function retrieveCallback($value)
	{
		if ($this->isCallable($value)) {
			return $value;
		}

		return function ($item) use ($value) {
			return Arr::get($item, $value);
		};
	}

	/**
	 * retrieve the given value is callable
	 *
	 * @param $value
	 * @return bool
	 */
	protected function isCallable($value)
	{
		return !\is_string($value) && \is_callable($value);
	}


	/**
	 * convert checker callback
	 *
	 * @param string     $key
	 * @param string     $operator
	 * @param null|mixed $value
	 * @return \Closure
	 */
	protected function convertForWhere($key, $operator, $value = null)
	{
		if (\func_num_args() === 2) {
			$value = $operator;

			$operator = '=';
		}

		return function ($item) use ($key, $operator, $value) {
			$source = Arr::get($item, $key, '');

			$valid = array_filter([$source, $value], '\is_string');

			if (\count($valid) < 2 && \count(array_filter([$source, $value], '\is_object')) === 1) {
				return \in_array($operator, ['!=', '!==', '<>']);
			}

			switch ($operator) {
				default:
				case '=':
				case '==':
					return $source == $value;
				case '!=':
				case '<>':
					return $source != $value;
				case '>':
					return $source > $value;
				case '<':
					return $source < $value;
				case '>=':
					return $source >= $value;
				case '<=':
					return $source <= $value;
				case '===':
					return $source === $value;
				case '!==':
					return $source !== $value;
			}
		};
	}
}