<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of published posts.
     */
    public function index()
    {
        $posts = Post::where('status', 'published')
                    ->latest()
                    ->paginate(10);

        return view('posts.index', compact('posts'));
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        // Ensure only published posts can be viewed
        if ($post->status !== 'published') {
            abort(404);
        }

        return view('posts.show', compact('post'));
    }
}