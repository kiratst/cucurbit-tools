<?php

namespace Cucurbit\Tools\Database\Connection;

use Cucurbit\Tools\Database\Connector\ConnectionException;
use Cucurbit\Tools\Database\Connector\MysqlConnector;
use Cucurbit\Tools\Database\Traits\ConfigTrait;

class ConnectionFactory
{
	use ConfigTrait;

	/**
	 * @var MysqlConnector|null
	 */
	public $connection;

	/**
	 * @var self
	 */
	private static $instance;

	/**
	 * @throws ConnectionException
	 */
	protected function make()
	{
		$driver = $this->getDriver();

		switch (strtolower($driver)) {
			default:
			case 'mysql':
				$connector        = new MysqlConnector();
				$this->connection = $connector;
				break;
		}

		return $this->connection;
	}

	public static function __callStatic($methods, $arguments)
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return \call_user_func_array([self::$instance, $methods], $arguments);
	}

}