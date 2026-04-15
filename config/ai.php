<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Drivers
    |--------------------------------------------------------------------------
    |
    | Sidekick abstracts AI providers behind three capabilities: chat, stt
    | (speech-to-text), and tts (text-to-speech). Swap providers per
    | capability via .env without touching feature code.
    |
    */

    'chat' => env('AI_CHAT_DRIVER', 'ollama'),
    'stt' => env('AI_STT_DRIVER', 'browser'),
    'tts' => env('AI_TTS_DRIVER', 'browser'),

    /*
    |--------------------------------------------------------------------------
    | Driver Configurations
    |--------------------------------------------------------------------------
    */

    'drivers' => [
        'ollama' => [
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'model' => env('OLLAMA_MODEL', 'gemma3:4b'),
            'timeout' => env('OLLAMA_TIMEOUT', 60),
        ],

        'claude' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'model' => env('CLAUDE_MODEL', 'claude-sonnet-4-6'),
            'max_tokens' => env('CLAUDE_MAX_TOKENS', 1024),
        ],

        'groq' => [
            'api_key' => env('GROQ_API_KEY'),
            'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        ],

        'openai_tts' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_TTS_MODEL', 'tts-1'),
            'voice' => env('OPENAI_TTS_VOICE', 'nova'),
        ],

        'elevenlabs' => [
            'api_key' => env('ELEVENLABS_API_KEY'),
            'model' => env('ELEVENLABS_MODEL', 'eleven_turbo_v2_5'),
        ],

        'whisper' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('WHISPER_MODEL', 'whisper-1'),
        ],
    ],
];
