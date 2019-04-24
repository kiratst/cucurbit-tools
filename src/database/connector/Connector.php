<?php

namespace Cucurbit\Tools\Database\Connector;

use Cucurbit\Tools\Database\Traits\ConfigTrait;
use Cucurbit\Tools\Database\Traits\ConnectorTrait;
use Throwable;

class Connector implements ConnectorInterface
{
	use ConnectorTrait, ConfigTrait;

	/**
	 * Connector constructor.
	 *
	 * @throws ConnectionException
	 */
	public function __construct()
	{
		try {
			$this->parseConfig();
			$this->pdo = $this->initPdo();
		} catch (Throwable $e) {
			throw new ConnectionException($e->getMessage());
		}
	}

	/**
	 * parse and init config
	 * @throws ConnectionException
	 */
	public function parseConfig()
	{
		$config = $this->getConfig();
		$driver = $this->getDriver();

		if (empty($config['connections'])) {
			throw new ConnectionException('Please setting the connections');
		}

		$connection = $config['connections'];

		if (empty($connection[$driver])) {
			throw new ConnectionException("Unsupported driver [{$driver}]");
		}

		$driver_config = $connection[$driver];

		$this->setDriver($driver);
		$this->setConfig($driver_config);
	}

}