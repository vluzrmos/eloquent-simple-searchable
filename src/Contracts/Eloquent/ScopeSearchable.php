<?php

namespace Vluzrmos\SimpleSearchable\Contracts\Eloquent;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;

interface ScopeSearchable
{
	/**
	 * Scope for search eloquent model with related columns.
	 *
	 * @param  QueryBuilder|EloquentQueryBuilder $query
	 * @param  string $text
	 * @param  array $searchable
	 * @return void
	 */
	public function scopeSearch($query, $text, $searchable = [ ]);
}