<?php

namespace Cucurbit\Tools\Database\Connector;

use PDO;

final class MysqlConnector extends Connector
{
	/**
	 * @return MysqlConnector
	 * @throws ConnectionException
	 */
	public function createConnection()
	{
		try {
			return $this->connect();
		} catch (\Throwable $e) {
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