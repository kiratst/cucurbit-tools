<?php

namespace Cucurbit\Tools\Database\Resolve;

use Cucurbit\Tools\Database\Builder\Builder;

/**
 * resolve builder to sql
 */
class Resolver implements ResolverInterface
{

	public function toSql(Builder $builder)
	{
		dd($builder);
	}
}