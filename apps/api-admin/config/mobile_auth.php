<?php

return [
    'access_token_ttl_minutes' => (int) env('MOBILE_ACCESS_TOKEN_TTL_MINUTES', 60),
    'refresh_token_ttl_minutes' => (int) env('MOBILE_REFRESH_TOKEN_TTL_MINUTES', 43200),
    'device_session_ttl_minutes' => (int) env('MOBILE_DEVICE_SESSION_TTL_MINUTES', 43200),
];
