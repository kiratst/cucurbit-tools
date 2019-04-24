<?php

namespace Cucurbit\Tools\Database\Connector;

/**
 * Interface connector
 * @package Cucurbit\Tools\Database\Connector
 */
interface ConnectorInterface
{
	/**
	 * create connection
	 *
	 * @return \PDO
	 */
	public function connect();
}