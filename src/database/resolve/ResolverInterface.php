<?php

namespace Cucurbit\Tools\Database\Resolve;

use Cucurbit\Tools\Database\Builder\Builder;

interface ResolverInterface
{
	/**
	 * @param Builder $builder
	 * @return mixed
	 */
	public function toSql(Builder $builder);
}