<?php

use App\Services\AI\AIManager;
use App\Services\AI\Contracts\ChatProvider;
use App\Services\AI\Drivers\ClaudeChatDriver;
use App\Services\AI\Drivers\OllamaChatDriver;
use Illuminate\Support\Facades\Http;

it('resolves the ollama driver by default', function () {
    config()->set('ai.chat', 'ollama');

    $provider = app(ChatProvider::class);

    expect($provider)->toBeInstanceOf(OllamaChatDriver::class);
});

it('resolves the claude driver when configured', function () {
    config()->set('ai.chat', 'claude');
    config()->set('ai.drivers.claude.api_key', 'test-key');

    $this->app->forgetInstance(ChatProvider::class);
    $this->app->forgetInstance(AIManager::class);

    expect(app(ChatProvider::class))->toBeInstanceOf(ClaudeChatDriver::class);
});

it('sends the system prompt and messages to ollama', function () {
    Http::fake([
        '*/api/chat' => Http::response([
            'message' => ['role' => 'assistant', 'content' => 'Hi there!'],
        ]),
    ]);

    $driver = new OllamaChatDriver('http://localhost:11434', 'gemma3:4b');

    $reply = $driver->reply(
        messages: [['role' => 'user', 'content' => 'hello']],
        systemPrompt: 'You are buddy.',
    );

    expect($reply)->toBe('Hi there!');

    Http::assertSent(function ($request) {
        $payload = $request->data();

        return $payload['model'] === 'gemma3:4b'
            && $payload['messages'][0]['role'] === 'system'
            && $payload['messages'][0]['content'] === 'You are buddy.'
            && $payload['messages'][1]['role'] === 'user';
    });
});

it('throws a clear error when ollama returns a failure', function () {
    Http::fake([
        '*/api/chat' => Http::response('boom', 500),
    ]);

    $driver = new OllamaChatDriver('http://localhost:11434', 'gemma3:4b');

    expect(fn () => $driver->reply([['role' => 'user', 'content' => 'hi']]))
        ->toThrow(RuntimeException::class, 'Ollama request failed');
});
