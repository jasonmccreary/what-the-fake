<?php

namespace App\Jobs;

use App\Models\Post;
use App\Support\LanguageAI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReviewPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function handle(LanguageAI $languageAI): void
    {
        $passes = $languageAI->analyze($this->post->body);

        if (! $passes) {
            $this->post->delete();

            return;
        }

        PublishPost::dispatch($this->post);
    }
}
