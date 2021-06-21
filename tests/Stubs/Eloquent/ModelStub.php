<?php

namespace Tests\Stubs\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Vluzrmos\SimpleSearchable\Contracts\Eloquent\ScopeSearchable;
use Vluzrmos\SimpleSearchable\Eloquent\SimpleSearchableTrait;

class ModelStub extends Model implements ScopeSearchable
{
    use SimpleSearchableTrait;
}
