<?php

namespace App\Services\AI\Contracts;

interface TtsProvider
{
    /**
     * Synthesize speech from text. Returns raw audio bytes.
     *
     * @param  array<string, mixed>  $options
     */
    public function synthesize(string $text, ?string $voice = null, array $options = []): string;

    /**
     * The mime type of audio this provider returns.
     */
    public function mimeType(): string;
}
