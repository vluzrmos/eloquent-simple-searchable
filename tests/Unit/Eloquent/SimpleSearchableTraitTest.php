<?php

namespace Tests\Unit\Eloquent;

use Tests\TestCase;
use Tests\Stubs\Eloquent\ModelStub;

class SimpleSearchableTraitTest extends TestCase
{
    public function testShouldHaveScopeSearch()
    {
        $this->assertTrue(method_exists($this->model, 'scopeSearch'));
        $this->assertFalse(property_exists($this->model, 'searchable'));

        $searchables = $this->model->searchableFields(['name' => 'full_text']);

        $this->assertTrue($searchables === ['name' => 'full_text']);
    }

    public function testShouldGenerateSQLWithFullTextSearch()
    {
        $query = $this->model->search('test', ['name' => 'full_text']);

        $sql = $query->toSql();
        $bindings = $query->getBindings();

        $this->assertStringContainsString('"model_stubs"."name" like ?', $sql);
        $this->assertArrayHasKey(0, $bindings);
        $this->assertEquals("%test%", $bindings[0]);
    }

    public function testShouldGenerateSQLWithLeftTextSearch()
    {
        $query = $this->model->search('test', ['name' => 'left_text']);

        $sql = $query->toSql();
        $bindings = $query->getBindings();

        $this->assertStringContainsString('"model_stubs"."name" like ?', $sql);
        $this->assertArrayHasKey(0, $bindings);
        $this->assertEquals("test%", $bindings[0]);
    }

    public function testShouldGenerateSQLWithRightTextSearch()
    {
        $query = $this->model->search('test', ['name' => 'right_text']);

        $sql = $query->toSql();
        $bindings = $query->getBindings();

        $this->assertStringContainsString('"model_stubs"."name" like ?', $sql);
        $this->assertArrayHasKey(0, $bindings);
        $this->assertEquals("%test", $bindings[0]);
    }

    public function testShouldGenerateSQLWithEqualsSearch()
    {
        $query = $this->model->search('test', ['name' => 'equals']);

        $sql = $query->toSql();
        $bindings = $query->getBindings();

        $this->assertStringContainsString('"model_stubs"."name" = ?', $sql);
        $this->assertArrayHasKey(0, $bindings);
        $this->assertEquals("test", $bindings[0]);
    }
}
