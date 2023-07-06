<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Jobs\ReviewPost;
use App\Mail\NewPost;
use Illuminate\Support\Facades\Mail;

class PrepareNewPost
{
    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        Mail::to(config('settings.admin_email'))
            ->queue(new NewPost($event->post));

        ReviewPost::dispatch($event->post);
    }
}
