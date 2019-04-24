<?php

namespace Cucurbit\Tools\Database\Connector;

use Cucurbit\Tools\Database\Traits\ConnectorTrait;
use Exception;
use PDO;

class Connector implements ConnectorInterface
{
	use ConnectorTrait;

	/**
	 * @var string
	 */
	private $init_config_path;

	/**
	 * parse and init config
	 * @throws Exception
	 */
	public function parseConfig()
	{
		$config = $this->getConfig();
		$driver = empty($config['driver']) ? 'mysql' : $config['driver'];

		if (empty($config['connections'])) {
			throw new Exception('please setting the connections');
		}

		$connection = $config['connections'];

		if (empty($connection[$driver])) {
			throw new Exception("Unsupported driver [{$driver}]");
		}

		$driver_config = $connection[$driver];

		$this->setDriver($driver);
		$this->setConfig($driver_config);
	}

	/**
	 * @return array|mixed
	 */
	protected function getConfig()
	{
		$path = $this->configPath();

		if (!file_exists($path)) {
			$init_config = $this->getInitConfig();

			copy($this->init_config_path, $path);
			return $init_config;
		}

		$all_config = require $path;

		return $all_config['database'] ?? [];
	}

	/**
	 * config path
	 *
	 * @return string
	 */
	private function configPath()
	{
		$base_path = getcwd();
		return $base_path . DIRECTORY_SEPARATOR . 'cucurbit.php';
	}

	/**
	 * @return mixed
	 */
	private function getInitConfig()
	{
		$init_path = \dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config/cucurbit.php';

		$this->init_config_path = $init_path;

		return require $init_path;
	}

}