<?php

namespace Tests\Feature\Jobs;

use App\Events\PostPublished;
use App\Jobs\PublishPost;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PublishPostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_updates_the_post_and_notifies_the_system(): void
    {
        Event::fake();

        $post = Post::factory()->create();

        PublishPost::dispatch($post);

        $post->refresh();
        $this->assertNotNull($post->published_at);

        Event::assertDispatched(PostPublished::class, function (PostPublished $event) use ($post) {
            return $event->post->is($post);
        });
    }
}
