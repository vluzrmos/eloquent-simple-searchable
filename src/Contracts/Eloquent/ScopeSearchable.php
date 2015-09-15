<?php

namespace Vluzrmos\SimpleSearchable\Contracts\Eloquent;

interface ScopeSearchable
{
	function scopeSearch($query, $text, $searchable = [ ]);
}