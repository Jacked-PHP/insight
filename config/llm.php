<?php

return [

    'request_timeout' => 30,

    'ai_api' => env('LLM_AI_API', 'ollama'),

    'http-headers' => [
        // 'OpenAI-Beta' => 'assistants=v1',
    ],

    // agent

    'agent' => [
        'stream' => env('LLM_AGENT_STREAM', true),
    ],

    // embedding

    'embedding' => [
        'model' => env('LLM_EMBEDDING_MODEL'),
        'api_key' => env('LLM_API_KEY'),
        'request_timeout' => env('LLM_REQUEST_TIMEOUT', 30),
        'base_uri' => env('LLM_BASE_URI', 'api.openai.com/v1'),
    ],

    // openai specific

    'openai-model' => env('LLM_MODEL', 'gpt-4'),
    'organization' => env('OPENAI_ORGANIZATION'),

];
