<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class LanguageAI
{
    public function analyze(string $body): bool
    {
        if (str_contains($body, 'facades suck')) {
            return false;
        }

        $response = Http::post('https://api.language.ai/v1/analyze', [
            'text' => $body,
        ]);

        return $response->successful();
    }
}
