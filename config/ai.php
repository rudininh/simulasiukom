<?php

return [
    'provider' => env('AI_PROVIDER', 'openai'),
    'api_key' => env('AI_API_KEY'),
    'model' => env('AI_MODEL', 'gpt-4o-mini'),
    'max_tokens' => (int) env('AI_MAX_TOKENS', 3000),
    'temperature' => (float) env('AI_TEMPERATURE', 0.3),
];
