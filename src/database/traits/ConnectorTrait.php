<?php

namespace Cucurbit\Tools\Database\Traits;

use Cucurbit\Tools\Database\Connector\ConnectionException;
use PDO;

/**
 * Trait connector
 *
 * @package Cucurbit\Tools\Database\Traits
 */
trait ConnectorTrait
{
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
	protected static $pdo;

	/**
	 * create database connection
	 *
	 * @return PDO
	 * @throws ConnectionException
	 */
	public function connect()
	{
		if (static::$pdo instanceof static) {
			return static::$pdo;
		}

		$this->parseConfig();
		return $this->initPdo();
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

	/**
	 * get dsn
	 *
	 * @return string
	 */
	protected function getDsn()
	{
		$host     = $this->host ?: '127.0.0.1';
		$database = $this->database ?: '';
		$port     = $this->port ?: 3306;
		$charset  = $this->charset ?: 'utf8';

		return "mysql:host=$host; dbname=$database;
					port=$port; charset=$charset";
	}

	/**
	 * set driver
	 *
	 * @param string $driver
	 */
	protected function setDriver($driver)
	{
		$this->driver = $driver;
	}

	/**
	 * set config
	 *
	 * @param array $config
	 * @throws ConnectionException
	 */
	protected function setConfig($config)
	{
		if (empty($config['host'])) {
			throw new ConnectionException('Database hosts is empty.');
		}

		if (empty($config['database'])) {
			throw new ConnectionException('Database database is empty');
		}

		$this->host     = $config['host'];
		$this->port     = empty($config['port']) ? 3306 : $config['port'];
		$this->database = $config['database'];
		$this->username = empty($config['username']) ? 'root' : $config['username'];
		$this->password = empty($config['password']) ? 'root' : $config['password'];
		$this->charset  = empty($config['charset']) ? 'utf8' : $config['charset'];
		$this->prefix   = empty($config['charset']) ? '' : $config['charset'];
	}

	private function __clone()
	{
	}
}