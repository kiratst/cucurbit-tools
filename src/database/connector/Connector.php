<?php

namespace Cucurbit\Tools\Database\Connector;

use Cucurbit\Tools\Database\Traits\ConfigTrait;
use Cucurbit\Tools\Database\Traits\ConnectorTrait;
use PDO;

abstract class Connector implements ConnectorInterface
{
	use ConnectorTrait, ConfigTrait;

	/**
	 * @var string
	 */
	public $host;

	/**
	 * @var string
	 */
	public $database;

	/**
	 * @var string
	 */
	public $username;

	/**
	 * @var string
	 */
	public $password;

	/**
	 * @var string|int
	 */
	public $port = 3306;

	/**
	 * @var string
	 */
	public $charset = 'utf8';

	/**
	 * @var string
	 */
	public $prefix = '';

	/**
	 * @var array
	 */
	public $options = [];

	/**
	 * @var string
	 */
	protected $driver = 'mysql';

	/**
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * prevent clone
	 */
	private function __clone()
	{
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