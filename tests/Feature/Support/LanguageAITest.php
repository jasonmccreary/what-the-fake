<?php

namespace Tests\Feature\Support;

use App\Support\LanguageAI;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * @see \App\Support\LanguageAI
 */
class LanguageAITest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_true_from_successful_call(): void
    {
        Http::fake([
            'api.language.ai/v1/analyze' => Http::response(),
        ]);

        $subject = new LanguageAI();

        $this->assertTrue($subject->analyze('Hello world!'));
    }

    /**
     * @test
     */
    public function it_returns_false_from_unsuccessful_call(): void
    {
        Http::fake([
            'api.language.ai/v1/analyze' => Http::response(status: 400),
        ]);

        $subject = new LanguageAI();

        $this->assertFalse($subject->analyze('Bad words...'));
    }
}
