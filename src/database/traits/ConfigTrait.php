<?php

namespace Cucurbit\Tools\Database\Traits;

use Cucurbit\Tools\Database\Connector\ConnectionException;

trait ConfigTrait
{
	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var string
	 */
	private $init_config_path;

	/**
	 * @return array|mixed
	 * @throws ConnectionException
	 */
	protected function getConfig()
	{
		if ($this->config) {
			return $this->config;
		}

		$path = $this->configPath();

		if (!file_exists($path)) {
			$this->config = $init_config = $this->getInitConfig();

			copy($this->init_config_path, $path);
			return $init_config;
		}

		$all_config = require $path;

		if (empty($all_config['database'])) {
			throw new ConnectionException('Database config is empty');
		}

		return $this->config = $all_config['database'];
	}

	/**
	 * @return string
	 * @throws ConnectionException
	 */
	protected function getDriver()
	{
		$config = $this->getConfig();
		return empty($config['driver']) ? 'mysql' : $config['driver'];
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