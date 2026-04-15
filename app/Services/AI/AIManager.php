<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\ChatProvider;
use App\Services\AI\Contracts\SttProvider;
use App\Services\AI\Contracts\TtsProvider;

class AIManager
{
    public function __construct(
        protected ChatProvider $chat,
        protected ?SttProvider $stt = null,
        protected ?TtsProvider $tts = null,
    ) {}

    public function chat(): ChatProvider
    {
        return $this->chat;
    }

    public function stt(): SttProvider
    {
        if (! $this->stt) {
            throw new \RuntimeException('No STT provider configured.');
        }

        return $this->stt;
    }

    public function tts(): TtsProvider
    {
        if (! $this->tts) {
            throw new \RuntimeException('No TTS provider configured.');
        }

        return $this->tts;
    }
}
