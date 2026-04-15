<?php

namespace App\Providers;

use App\Services\AI\AIManager;
use App\Services\AI\Contracts\ChatProvider;
use App\Services\AI\Contracts\SttProvider;
use App\Services\AI\Contracts\TtsProvider;
use App\Services\AI\Drivers\ClaudeChatDriver;
use App\Services\AI\Drivers\OllamaChatDriver;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ChatProvider::class, function (Application $app): ChatProvider {
            return $this->resolveChat($app['config']->get('ai.chat'));
        });

        $this->app->singleton(AIManager::class, function (Application $app): AIManager {
            return new AIManager(
                chat: $app->make(ChatProvider::class),
                stt: $this->resolveStt($app['config']->get('ai.stt')),
                tts: $this->resolveTts($app['config']->get('ai.tts')),
            );
        });
    }

    protected function resolveChat(string $driver): ChatProvider
    {
        $config = config("ai.drivers.{$driver}", []);

        return match ($driver) {
            'ollama' => new OllamaChatDriver(
                baseUrl: $config['base_url'],
                model: $config['model'],
                timeout: (int) $config['timeout'],
            ),
            'claude' => new ClaudeChatDriver(
                apiKey: $config['api_key'] ?? throw new InvalidArgumentException('ANTHROPIC_API_KEY missing.'),
                model: $config['model'],
                maxTokens: (int) $config['max_tokens'],
            ),
            default => throw new InvalidArgumentException("Unknown chat driver: {$driver}"),
        };
    }

    protected function resolveStt(string $driver): ?SttProvider
    {
        // 'browser' means client-side Web Speech API — no server driver needed.
        return match ($driver) {
            'browser' => null,
            default => throw new InvalidArgumentException("Unknown stt driver: {$driver}"),
        };
    }

    protected function resolveTts(string $driver): ?TtsProvider
    {
        return match ($driver) {
            'browser' => null,
            default => throw new InvalidArgumentException("Unknown tts driver: {$driver}"),
        };
    }
}
