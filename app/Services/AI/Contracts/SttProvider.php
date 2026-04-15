<?php

namespace App\Services\AI\Contracts;

interface SttProvider
{
    /**
     * Transcribe audio bytes to text.
     *
     * @param  array<string, mixed>  $options
     */
    public function transcribe(string $audioBytes, string $mimeType = 'audio/webm', array $options = []): string;
}
