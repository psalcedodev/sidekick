<?php

namespace App\Services\AI\Drivers;

use App\Services\AI\Contracts\ChatProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ClaudeChatDriver implements ChatProvider
{
    public function __construct(
        protected string $apiKey,
        protected string $model,
        protected int $maxTokens = 1024,
    ) {}

    public function reply(array $messages, ?string $systemPrompt = null, array $options = []): string
    {
        $payload = array_filter([
            'model' => $options['model'] ?? $this->model,
            'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
            'system' => $systemPrompt,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? null,
        ], fn ($v) => $v !== null);

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', $payload);

        if ($response->failed()) {
            throw new RuntimeException("Claude request failed: {$response->status()} {$response->body()}");
        }

        return trim(collect($response->json('content', []))
            ->where('type', 'text')
            ->pluck('text')
            ->implode(''));
    }
}
