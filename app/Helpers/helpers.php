<?php

use Illuminate\Database\Eloquent\Builder;

if (!function_exists('applyMultiNumericFilterIfValid')) {
    function applyMultiNumericFilterIfValid(
        Builder $query,
        string $field,
        ?string $input
    ): Builder {
        if (!$input) {
            return $query;
        }

        $map = [
            'eq'  => '=',
            'gt'  => '>',
            'lt'  => '<',
            'gte' => '>=',
            'lte' => '<=',
        ];

        foreach (explode(',', $input) as $filter) {
            if (!preg_match('/^(eq|gt|lt|gte|lte):\d+$/', $filter)) {
                continue;
            }

            [$op, $value] = explode(':', $filter);
            $query->where($field, $map[$op], (int) $value);
        }

        return $query;
    }
}
