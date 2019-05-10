<?php

namespace Cucurbit\Tools\Config;

use ArrayAccess;
use Cucurbit\Tools\Support\Collection;
use Cucurbit\Tools\Common\Traits\ArrayAttributeTrait;
use Cucurbit\Tools\Config\Interfaces\ConfigInterface;

/**
 * config repository
 */
class Config implements ConfigInterface, ArrayAccess
{
	use ArrayAttributeTrait;

	/**
	 * @var array
	 */
	public $data;

	/**
	 * @var Collection
	 */
	public $collection;

	public function __construct($data = [])
	{
		$this->collection = new Collection($data);
	}

	public function set($key, $value = null)
	{
		$this->data = $this->collection->put($key, $value)->toArray();
	}

	public function get($key)
	{
		return $this->collection->get($key);
	}

	public function has($key)
	{
		return $this->collection->has($key);
	}

	public function all()
	{
		return $this->collection->all();
	}

}