<?php

namespace Cucurbit\Tools\Database\Connector;

use Cucurbit\Tools\Database\Traits\ConfigTrait;

class ConnectionFactory
{
	use ConfigTrait;

	/**
	 * @var static
	 */
	protected static $instance;

	/**
	 * @var array
	 */
	private static $connector = [];

	private function __construct()
	{

	}

	/**
	 * @throws ConnectionException
	 */
	protected function make()
	{
		$driver = strtolower($this->getDriver());

		if (!empty(self::$connector[$driver])) {
			return self::$connector[$driver];
		}

		switch ($driver) {
			default:
			case 'mysql':
				$connector = new MysqlConnector();
				self::$connector[$driver] = $connector;
				break;
		}

		return $connector;
	}

	public static function __callStatic($methods, $arguments)
	{
		if (static::$instance === null) {
			static::$instance = new static();
		}

		return \call_user_func_array([static::$instance, $methods], $arguments);
	}
}