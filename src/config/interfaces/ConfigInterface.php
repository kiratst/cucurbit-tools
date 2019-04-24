<?php

namespace Cucurbit\Tools\Config\Interfaces;

interface ConfigInterface
{
	/**
	 * @param string $key
	 * @param mixed  $value
	 * @return mixed
	 */
	public function set($key, $value = null);

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get($key);

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function has($key);

	/**
	 * @return mixed
	 */
	public function all();

}