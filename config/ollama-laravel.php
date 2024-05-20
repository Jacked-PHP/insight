<?php

// Config for Cloudstudio/Ollama

return [
    'model' => env('LLM_MODEL', 'llama3'),
    'embedding_model' => env('LLM_EMBEDDING_MODEL', 'mxbai-embed-large'),
    'debug' => env('LLM_DEBUG', false),

    'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
    'key' => env('OLLAMA_KEY', ''),
    'default_prompt' => env('OLLAMA_DEFAULT_PROMPT', 'Hello, how can I assist you today?'),
    'connection' => [
        'timeout' => env('OLLAMA_CONNECTION_TIMEOUT', 300),
    ],
    'temperature' => env('OLLAMA_TEMPERATURE', 0.8),
    'agent' => env('OLLAMA_AGENT', 'You are a personal secretary.'),


    'ai_api' => env('AI_API', 'ollama'),
];
