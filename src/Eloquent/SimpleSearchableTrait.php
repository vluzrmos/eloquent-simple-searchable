<?php

namespace Vluzrmos\SimpleSearchable\Eloquent;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Support\Str;

/**
 * Class SimpleSearchableTrait
 * @method static QueryBuilder|EloquentQueryBuilder search($text, array $searchable=[])
 */
trait SimpleSearchableTrait
{
    /**
     * Scope for search eloquent model with related columns.
     *
     * @param  QueryBuilder|EloquentQueryBuilder $query
     * @param  string $text
     * @param  array $searchable
     * @return void
     */
    public function scopeSearch($query, $text, $searchable = [])
    {
        $searchable = $this->searchableFields($searchable);

        if (empty($searchable)) {
            return;
        }

        $query->where(function ($query) use ($text, $searchable) {
            $this->processSearchableFields($query, $text, $searchable);
        });
    }

    /**
     * @param QueryBuilder|EloquentQueryBuilder $query
     * @param string $text
     * @param array $searchable
     */
    protected function processSearchableFields($query, $text, array $searchable)
    {
        foreach ($searchable as $field => $type) {
            $where = $this->getWhereConditionMethodName($type);

            list($relation, $column) = $this->splitFieldWithRelation($field);

            $table = $this->getRelationTable($relation, $this->getTable());
            
            $callback = function ($query) use ($table, $column, $text, $where) {
                $this->{$where}($query, $table.'.'.$column, $text);
            };

            $this->applyQueryConditionCallback($query, $relation, $callback);
        }
    }

    /**
     * @param QueryBuilder|EloquentQueryBuilder $query
     * @param string|null $relation
     * @param callable|\Closure $callback
     */
    protected function applyQueryConditionCallback($query, $relation, $callback)
    {
        if ($relation) {
            return $query->orWhereHas($relation, $callback);
        }

        return $query->orWhere($callback);
    }

    protected function getWhereConditionMethodName($type)
    {
        return 'process' . Str::studly($type) . 'Where';
    }

    protected function getRelationTable($relation, $default = null)
    {
        if (empty($relation) || !method_exists($this, $relation)) {
            return $default;
        }

        $relationInstance = $this->$relation();

        return $relationInstance
            ->getRelated()
            ->getTable();
    }

    /**
     * @param array $replacements
     * @return array
     */
    public function searchableFields(array $replacements = [])
    {
        if (empty($replacements) && property_exists($this, 'searchable')) {
            return $this->searchable;
        }

        return $replacements;
    }

    /**
     * Split a field into your relation and column name.
     *
     * @param  string $field
     * @return array
     */
    protected function splitFieldWithRelation($field)
    {
        $parts = preg_split('/\./', $field);

        // in cases of field is not from a relation
        if (count($parts) == 1) {
            return [null, $parts[0]];
        }

        //all parts until the last one is a relationship
        $relation = array_slice($parts, 0, -1);

        //The last one is a column name
        $column = end($parts);

        return [implode('.', $relation), $column];
    }

    /**
     * Process Where Like query
     * @param  QueryBuilder|EloquentQueryBuilder $query
     * @param  string $column
     * @param  string $text
     * @return mixed
     */
    protected function processLikeWhere($query, $column, $text)
    {
        return $query->where($column, 'like', '%' . $this->quoteToLikeStatement($text) . '%');
    }

    /**
     * Process Where Equals query
     * @param  QueryBuilder|EloquentQueryBuilder $query
     * @param  string $column
     * @param  string $text
     * @return mixed
     */
    protected function processEqualsWhere($query, $column, $text)
    {
        return $query->where($column, '=', $text);
    }

    /**
     * Process Full Text where query
     * @param  QueryBuilder|EloquentQueryBuilder $query
     * @param  string $column
     * @param  string $text
     * @return mixed
     */
    protected function processFullTextWhere($query, $column, $text)
    {
        return $query->where($column, 'like', '%' . preg_replace('/\s+/', '%', $this->quoteToLikeStatement($text)) . '%');
    }

    /**
     * Process Where Left Text Query
     * @param  QueryBuilder|EloquentQueryBuilder $query
     * @param  string $column
     * @param  string $text
     * @return mixed
     */
    protected function processLeftTextWhere($query, $column, $text)
    {
        return $query->where($column, 'like', $this->quoteToLikeStatement($text) . '%');
    }

    /**
     * Process Where Right Text Query
     * @param  QueryBuilder|EloquentQueryBuilder $query
     * @param  string $column
     * @param  string $text
     * @return mixed
     */
    protected function processRightTextWhere($query, $column, $text)
    {
        return $query->where($column, 'like', '%' . $this->quoteToLikeStatement($text));
    }

    /**
     * @param string $text
     * @return string
     */
    protected function quoteToLikeStatement($text)
    {
        return addcslashes($text, '%_.');
    }
}
