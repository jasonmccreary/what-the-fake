<?php

namespace Tests\Feature\Http\Controllers;

use App\Jobs\ReviewPost;
use App\Mail\NewPost;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PostController
 */
class PostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_displays_posts(): void
    {
        $posts = Post::factory()->count(3)->createQuietly();

        $response = $this->get(route('post.index'));

        $response->assertOk();
        $response->assertViewIs('post.index');
        $response->assertViewHas('posts', $posts);
    }

    /**
     * @test
     */
    public function create_displays_form(): void
    {
        $response = $this->get(route('post.create'));

        $response->assertOk();
        $response->assertViewIs('post.create');
    }

    /**
     * @test
     */
    public function store_saves_posts_and_begins_review(): void
    {
        $title = $this->faker->sentence();
        $body = $this->faker->paragraphs(3, true);
        $author = $this->faker->name();

        Mail::fake();
        Bus::fake();

        $response = $this->post(route('post.store'), [
            'title' => $title,
            'body' => $body,
            'author' => $author,
        ]);

        $this->assertDatabaseCount('posts', 1);
        $post = Post::first();

        $this->assertSame($title, $post->title);
        $this->assertSame($body, $post->body);
        $this->assertSame($author, $post->author);
        $this->assertNull($post->published_at);

        $response->assertRedirect(route('post.show', $post));

        Bus::assertDispatched(ReviewPost::class, function ($job) use ($post) {
            return $job->post->is($post);
        });

        Mail::assertQueued(NewPost::class, function ($mail) use ($post) {
            return $mail->hasTo('hello@example.com') &&
                $mail->post->is($post);
        });
        Mail::assertOutgoingCount(1);
    }

    /**
     * @test
     */
    public function show_displays_view(): void
    {
        $post = Post::factory()->createQuietly();

        $response = $this->get(route('post.show', $post));

        $response->assertOk();
        $response->assertViewIs('post.show');
        $response->assertViewHas('post', $post);
    }
}
