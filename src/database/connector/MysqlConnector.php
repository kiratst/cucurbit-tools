<?php

namespace Cucurbit\Tools\Database\Connector;

use Cucurbit\Tools\Database\Resolver\ResolverFactory;
use PDO;
use Throwable;

class MysqlConnector extends Connector
{
	/**
	 * Connector constructor.
	 *
	 * @throws ConnectionException
	 */
	public function __construct()
	{
		try {
			$this->parseConfig();
			$this->pdo      = $this->initPdo();
			$this->resolver = ResolverFactory::make($this->driver, $this->prefix);
		} catch (Throwable $e) {
			throw new ConnectionException($e->getMessage());
		}
	}

	/**
	 * create pdo instance
	 *
	 * @return PDO
	 * @throws ConnectionException
	 */
	protected function initPdo()
	{
		try {
			return new PDO($this->getDsn(), $this->username, $this->password, $this->options);
		} catch (\Throwable $e) {
			throw new ConnectionException($e->getMessage());
		}
	}

}