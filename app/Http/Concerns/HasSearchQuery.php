<?php

namespace App\Http\Concerns;

trait HasSearchQuery
{
    public function buildSearchQuery(array $queries): array
    {
        $api_search_queries = $this->api_search_queries ?? [];
        $search_string = [];

        foreach ($queries as $query_name => $query) {
            $api_search_param = $api_search_queries[$query_name] ?? '';
            if ($query && $api_search_param) {
                $search_string[] = "$api_search_param:$query";
            }
        }

        return ['search' => implode(',', $search_string)];
    }
}