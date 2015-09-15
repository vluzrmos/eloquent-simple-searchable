<?php

namespace Vluzrmos\SimpleSearchable\Eloquent;

trait SimpleSearchableTrait
{
    /**
     * Scope for search eloquent model with related columns.
     *
     * @param  mixed $query
     * @param  string $text
     * @param  array $searchable
     * @return void
     */
    public function scopeSearch($query, $text, $searchable = [])
    {
        $searchable = empty($searchable) ? $this->searchable : $searchable;

        $query->where(function ($query) use ($text, $searchable) {
            foreach ($searchable as $field => $type) {
                $where = 'process' . studly_case($type) . 'Where';

                list($relation, $column) = $this->splitFieldWithRelation($field);

                $callback = function ($query) use ($column, $type, $text, $where) {
                    $this->{$where}($query, $column, $text);
                };

                if ($relation) {
                    $query->orWhereHas($relation, $callback);
                } else {
                    $query->orWhere($callback);
                }
            }
        });
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
     * @param  mixed $query
     * @param  string $column
     * @param  string $text
     * @return mixed
     */
    protected function processLikeWhere($query, $column, $text)
    {
        return $query->where($column, 'like', '%' . addcslashes($text, '%_.') . '%');
    }

    /**
     * Process Where Equals query
     * @param  mixed $query
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
     * @param  mixed $query
     * @param  string $column
     * @param  string $text
     * @return mixed
     */
    protected function processFullTextWhere($query, $column, $text)
    {
        return $query->where($column, 'like', '%' . preg_replace('/\s+/', '%', addcslashes($text, '%_.')) . '%');
    }

    /**
     * Process Where Left Text Query
     * @param  mixed $query
     * @param  string $column
     * @param  string $text
     * @return mixed
     */
    protected function processLeftTextWhere($query, $column, $text)
    {
        return $query->where($column, 'like', addcslashes($text, '%_.') . '%');
    }

    /**
     * Process Where Right Text Query
     * @param  mixed $query
     * @param  string $column
     * @param  string $text
     * @return mixed
     */
    protected function processRightTextWhere($query, $column, $text)
    {
        return $query->where($column, 'like', '%' . addcslashes($text, '%_.'));
    }
}
