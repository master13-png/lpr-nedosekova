<?php

return [
    'student' => env('LAB_STUDENT', 'Не указан'),
    'slug' => env('LAB_SLUG', 'unknown'),
    'group' => env('LAB_GROUP', ''),
    'number' => (int) env('LAB_N', 0),
    'code' => env('LAB_CODE', '—'),
    'per_page' => (int) env('LAB_PER_PAGE', 5),
    'min_len' => (int) env('LAB_MIN_LEN', 4),
    'str_len' => (int) env('LAB_STR_LEN', 142),
    'sort' => env('LAB_SORT', 'created_at DESC'),
];
