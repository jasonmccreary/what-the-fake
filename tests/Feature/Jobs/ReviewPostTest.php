<?php

namespace Tests\Feature\Jobs;

use App\Jobs\PublishPost;
use App\Jobs\ReviewPost;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ReviewPostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_publishes_the_post(): void
    {
        $post = Post::factory()->createQuietly();

        Bus::fake()->except(ReviewPost::class);

        ReviewPost::dispatch($post);

        $this->assertModelExists($post);

        $post->refresh();
        $this->assertNull($post->published_at);

        Bus::assertDispatched(PublishPost::class, function ($job) use ($post) {
            return $job->post->is($post);
        });
    }

    /**
     * @test
     */
    public function it_deletes_the_post_with_profanity(): void
    {
        $post = Post::factory()->createQuietly(['body' => 'facades suck!']);

        Bus::fake()->except(ReviewPost::class);

        ReviewPost::dispatch($post);

        $this->assertModelMissing($post);

        Bus::assertNotDispatched(PublishPost::class);
    }
}
