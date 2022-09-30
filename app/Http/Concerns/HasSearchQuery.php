<?php

namespace App\Http\Concerns;

trait HasSearchQuery
{
    public function buildSearchQuery(array $queries): array
    {
        $api_search_queries = $this->api_search_queries ?? [];
        $search_strings = [];

        foreach ($queries as $query_name => $query) {
            $api_search_param = $api_search_queries[$query_name] ?? '';
            if ($query && $api_search_param) {
                $search_strings[] = "$api_search_param:$query";
            }
        }

        return count($search_strings) ? ['search' => implode(',', $search_strings)] : [];
    }
}