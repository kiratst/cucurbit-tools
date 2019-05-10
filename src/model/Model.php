<?php

namespace Cucurbit\Tools\Model;

use Cucurbit\Tools\Database\Builder\Builder;
use Cucurbit\Tools\Database\Connector\ConnectionException;
use Cucurbit\Tools\Database\Connector\ConnectorFactory;
use Cucurbit\Tools\Database\Dao\Dao;

class Model
{
	/**
	 * @return Builder
	 * @throws ConnectionException
	 */
	protected function newBuilder()
	{
		return new Builder($this->getDao());
	}

	/**
	 * @return Dao
	 * @throws ConnectionException
	 */
	protected function getDao()
	{
		return new Dao($this->getConnector());
	}

	/**
	 * @return \Cucurbit\Tools\Database\Connector\MysqlConnector|mixed
	 * @throws ConnectionException
	 */
	protected function getConnector()
	{
		try {
			return ConnectorFactory::make();
		} catch (ConnectionException $e) {
			throw new ConnectionException($e->getMessage());
		}
	}

	/**
	 * @param $method
	 * @param $arguments
	 * @return mixed
	 * @throws ConnectionException
	 */
	public function __call($method, $arguments)
	{
		return \call_user_func_array([$this->newBuilder(), $method], $arguments);
	}

	/**
	 * @param $method
	 * @param $arguments
	 * @return mixed
	 */
	public static function __callStatic($method, $arguments)
	{
		$instance = new static();
		return \call_user_func_array([$instance, $method], $arguments);
	}
}