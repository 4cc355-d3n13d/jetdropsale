<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class AbstractFilter
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * The Eloquent builder.
     * @var Builder
     */
    protected $builder;

    /**
     * Registered filters to operate upon.
     */
    protected $filters = [];

    /**
     * Create a new AbstractFilters instance.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the filters.
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value) {
            $method = camel_case($filter);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this->builder;
    }

    /**
     * Fetch all relevant filters from the request.
     */
    public function getFilters(): array
    {
        return array_filter($this->request->only($this->filters));
    }

    /**
     * Escape special characters for a LIKE query.
     * @seen https://stackoverflow.com/a/42028380/329062
     */
    protected function escapeLike(string $value, string $char = '\\'): string
    {
        return str_replace(
            [$char, '%', '_'],
            [$char.$char, $char.'%', $char.'_'],
            $value
        );
    }
}
