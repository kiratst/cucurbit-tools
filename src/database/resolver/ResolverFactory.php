<?php

namespace Cucurbit\Tools\Database\Resolver;

use Cucurbit\Tools\Database\Connector\ConnectionException;

class ResolverFactory
{
	private $types = [
		'mysql',
	];

	/**
	 * @var array
	 */
	private static $resolvers = [];

	/**
	 * @var self
	 */
	private static $instance;

	private function __construct()
	{

	}

	/**
	 * @param string $type
	 * @return Resolver
	 * @throws ConnectionException
	 */
	protected function make($type = 'mysql')
	{
		$type = strtolower($type);

		if (!\in_array($type, $this->types, true)) {
			throw new ConnectionException('the type of resolver does not support');
		}

		if (!empty(self::$resolvers[$type])) {
			return self::$resolvers[$type];
		}


		switch ($type) {
			default:
			case 'mysql':
				$resolver = new MysqlResolver();
				break;
		}

		self::$resolvers[$type] = $resolver;

		return $resolver;
	}

	public static function __callStatic($methods, $arguments)
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return \call_user_func_array([self::$instance, $methods], $arguments);
	}
}