<?php

namespace App\Services\AI\Contracts;

interface ChatProvider
{
    /**
     * Generate a reply from the model.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options
     */
    public function reply(array $messages, ?string $systemPrompt = null, array $options = []): string;
}
