<?php

return array(
    'dsn' => env('SENTRY_LARAVEL_DSN'),

    // capture release as git sha
    'release' => env('APP_ENV'),

    // Capture bindings on SQL queries
    'breadcrumbs.sql_bindings' => true,

    // Capture default user context
    'user_context' => false,
);
