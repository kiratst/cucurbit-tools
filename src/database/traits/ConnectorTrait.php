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
	private $pdo;

	/**
	 * @var null|static
	 */
	protected static $connection;

	/**
	 * create database connection
	 *
	 */
	public function connect()
	{
		if (!static::$connection instanceof static) {
			static::$connection = new static();
		}

		return static::$connection;
	}

	/**
	 * create pdo instance
	 *
	 * @return static
	 * @throws ConnectionException
	 */
	protected function initPdo()
	{
		try {
			$this->pdo = new PDO($this->getDsn(), $this->username, $this->password, $this->options);
			return $this;
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
			throw new ConnectionException('Database name is empty');
		}

		$this->host     = $config['host'];
		$this->port     = empty($config['port']) ? 3306 : $config['port'];
		$this->database = $config['database'];
		$this->username = empty($config['username']) ? 'root' : $config['username'];
		$this->password = empty($config['password']) ? 'root' : $config['password'];
		$this->charset  = empty($config['charset']) ? 'utf8' : $config['charset'];
		$this->prefix   = empty($config['prefix']) ? '' : $config['prefix'];
	}

	/**
	 * prevent clone
	 */
	private function __clone()
	{
	}
}