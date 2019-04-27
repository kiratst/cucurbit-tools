<?php

namespace Cucurbit\Tools\Database\Resolver;

use Cucurbit\Tools\Database\Builder\Builder;

class MysqlResolver extends Resolver
{
	public function toSql(Builder $builder)
	{
		dd($builder);
	}
}