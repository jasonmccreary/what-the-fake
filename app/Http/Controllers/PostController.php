<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePost;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('post.index', [
            'posts' => Post::all(),
        ]);
    }

    public function create(): View
    {
        return view('post.create');
    }

    public function store(StorePost $request): RedirectResponse
    {
        $post = Post::create($request->safe()->all());

        return redirect()->route('post.show', $post);
    }

    public function show(Post $post): View
    {
        return view('post.show', ['post' => $post]);
    }

    public function edit(Post $post): View
    {
        return view('post.edit', ['post' => $post]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $post->update($request->safe());

        return redirect()->route('post.show', $post);
    }

    public function destroy(Post $post)
    {
        //
    }
}
