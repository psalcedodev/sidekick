<?php

namespace App\Services\AI\Drivers;

use App\Services\AI\Contracts\ChatProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OllamaChatDriver implements ChatProvider
{
    public function __construct(
        protected string $baseUrl,
        protected string $model,
        protected int $timeout = 60,
    ) {}

    public function reply(array $messages, ?string $systemPrompt = null, array $options = []): string
    {
        $payload = [
            'model' => $options['model'] ?? $this->model,
            'messages' => $this->buildMessages($messages, $systemPrompt),
            'stream' => false,
            'options' => array_filter([
                'temperature' => $options['temperature'] ?? null,
                'num_predict' => $options['max_tokens'] ?? null,
            ], fn ($v) => $v !== null),
        ];

        $response = Http::timeout($this->timeout)
            ->post(rtrim($this->baseUrl, '/').'/api/chat', $payload);

        if ($response->failed()) {
            throw new RuntimeException("Ollama request failed: {$response->status()} {$response->body()}");
        }

        return trim($response->json('message.content', ''));
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array<int, array{role: string, content: string}>
     */
    protected function buildMessages(array $messages, ?string $systemPrompt): array
    {
        if ($systemPrompt === null || $systemPrompt === '') {
            return $messages;
        }

        return array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $messages,
        );
    }
}
