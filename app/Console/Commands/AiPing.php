<?php

namespace App\Console\Commands;

use App\Services\AI\AIManager;
use Illuminate\Console\Command;

class AiPing extends Command
{
    protected $signature = 'ai:ping {prompt=Say hi in one short sentence.}';

    protected $description = 'Smoke-test the configured AI chat provider.';

    public function handle(AIManager $ai): int
    {
        $prompt = (string) $this->argument('prompt');

        $this->info("Driver: ".config('ai.chat'));
        $this->info("Prompt: {$prompt}");
        $this->newLine();

        try {
            $reply = $ai->chat()->reply(
                messages: [['role' => 'user', 'content' => $prompt]],
                systemPrompt: 'You are a warm, friendly companion. Reply in one short sentence.',
            );
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->line("Reply: {$reply}");

        return self::SUCCESS;
    }
}
