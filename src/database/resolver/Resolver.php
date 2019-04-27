<?php

namespace Cucurbit\Tools\Database\Resolver;

use Cucurbit\Tools\Database\Builder\Builder;

/**
 * resolve builder to sql
 */
abstract class Resolver implements ResolverInterface
{

	public function toSql(Builder $builder)
	{
		dd($builder);
	}
}