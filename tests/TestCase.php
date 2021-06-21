<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Tests\Stubs\Eloquent\ModelStub;
use Vluzrmos\SimpleSearchable\Contracts\Eloquent\ScopeSearchable;
use Vluzrmos\SimpleSearchable\Eloquent\SimpleSearchableTrait;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $model;

    public function setUp(): void
    {
        parent::setUp();

        $usages = class_uses_recursive(ModelStub::class);

        $trait = SimpleSearchableTrait::class;

        $this->assertContains($trait, $usages);

        $model = new ModelStub();

        $this->assertInstanceOf(ScopeSearchable::class, $model);
        $this->assertInstanceOf(Model::class, $model);

        $this->model = $model;
    }
}
