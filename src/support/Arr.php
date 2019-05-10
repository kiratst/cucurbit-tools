<?php

namespace Cucurbit\Tools\Support;

use ArrayAccess;

class Arr
{
	/**
	 * get the first item
	 *
	 * @param $data
	 * @return mixed
	 */
	public static function first($data)
	{
		return reset($data);
	}

	/**
	 * get the last item
	 *
	 * @param $data
	 * @return mixed
	 */
	public static function last($data)
	{
		return end($data);
	}

	/**
	 * array random
	 *
	 * @param      $data
	 * @param null $number
	 * @return array|mixed
	 */
	public static function random($data, $number = null)
	{
		$required = \is_null($number) ? 1 : $number;

		$count = \count($data);

		if ($required > $count) {
			$number = $count;
		}

		if ($number === null) {
			return $data[array_rand($data)];
		}

		if ((int) $number === 0) {
			return [];
		}

		$keys = array_rand($data, $number);

		$result = [];

		foreach ((array) $keys as $key) {
			$result[] = $data[$key];
		}

		return $result;
	}

	/**
	 * shuffle the items
	 * @param $data
	 * @return mixed
	 */
	public static function shuffle($data)
	{
		shuffle($data);

		return $data;
	}

	/**
	 * Filter the array
	 *
	 * @param array    $array
	 * @param callable $callback
	 * @return array
	 */
	public static function where($array, callable $callback)
	{
		return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
	}

	/**
	 * Get item
	 *
	 * @param      $data
	 * @param null $key
	 * @param null $default
	 * @return mixed|null
	 */
	public static function get($data, $key = null, $default = null)
	{
		if (!$key) {
			return $data;
		}

		if (self::accessible($data) && self::exists($data, $key)) {
			$result = $data[$key];
		}
		elseif (\is_object($data) && isset($data->{$key})) {
			$result = $data->{$key};
		}
		else {
			$result = $default;
		}

		return $result;
	}

	/**
	 * the value is array accessible
	 *
	 * @param $data
	 * @return bool
	 */
	public static function accessible($data)
	{
		return \is_array($data) || $data instanceof \ArrayAccess;
	}

	/**
	 * retrieve the  key exists
	 *
	 * @param ArrayAccess|array $data
	 * @param string|int        $key
	 * @return bool
	 */
	public static function exists($data, $key)
	{
		if ($data instanceof ArrayAccess) {
			return $data->offsetExists($key);
		}

		return array_key_exists($key, $data);
	}

	/**
	 * @param array $array
	 * @param       $depth
	 * @return array
	 */
	public static function flatten($array, $depth = INF)
	{
		$result = [];

		foreach ($array as $item) {
			$item = $item instanceof Collection ? $item->all() : $item;

			if (!\is_array($item)) {
				$result[] = $item;
			}
			elseif ($depth === 1) {
				$result = array_merge($result, array_values($item));
			}
			else {
				$result = array_merge($result, static::flatten($item, $depth - 1));
			}
		}

		return $result;
	}

	/**
	 * @param array        $array
	 * @param array|string $excepts
	 * @return array
	 */
	public static function except($array, $excepts)
	{
		$excepts = (array) $excepts;

		if (\count($excepts) === 0) {
			return $array;
		}

		foreach ($excepts as $except) {
			if (static::exists($array, $except)) {
				unset($array[$except]);
			}
		}

		dd($array);

	}
}