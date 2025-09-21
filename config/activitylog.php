<?php

return [
    'default_log_name' => 'default',
    'activity_model' => Spatie\Activitylog\Models\Activity::class,
    'table_name' => 'activity_log',
    'database_connection' => env('ACTIVITY_LOGGER_DB_CONNECTION'),
    'queue' => env('ACTIVITY_LOGGER_QUEUE'),
];
