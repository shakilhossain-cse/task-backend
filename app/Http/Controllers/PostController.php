<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    //get all posts
    public function index()
    {
        $posts = Post::with(['user', 'reactions.reaction'])->latest()->get();

        foreach ($posts as $post) {
            $postReactions = $post->reactions->groupBy('reaction.title')->map(function ($item) {
                return [
                    'title' => $item->first()->reaction->title,
                    'count' => $item->count(),
                    'user'=> $item->pluck('user_id')->contains(Auth::id()),
                ];
            })->values()->toArray();

            $post->reactionsData = $postReactions;
        }

        return response()->json(['posts' => $posts]);
    }

    // store post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $user = auth()->user();

        $imageNames = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/images', $imageName);
                $imageNames[] = asset('storage/images/' . $imageName);
            }
        }


        $user->posts()->create([
            'title' => $request->title,
            'images' => $imageNames,
        ]);


        return response()->json(["message" => 'post created successfully']);
    }

    // show post details
    public function show($id)
    {

        $authenticatedUser = Auth::user();

        $post = Post::with(['user', 'comments.user', 'comments.replies', 'comments.reactions.reaction', 'comments.replies.user', 'comments.replies.reactions.reaction', 'reactions.reaction'])->findOrFail($id);
        $postReactions = $post->reactions->groupBy('reaction.title')->map(function ($item) use ($authenticatedUser) {
            return [
                'title' => $item->first()->reaction->title,
                'count' => $item->count(),
                'user' => $item->pluck('user_id')->contains($authenticatedUser->id),
            ];
        })->values()->toArray();
        $post->reactionsData = $postReactions;

        foreach ($post->comments as $comment) {
            $commentReactions = $comment->reactions->groupBy('reaction.title')->map(function ($item) use ($authenticatedUser) {
                return [
                    'title' => $item->first()->reaction->title,
                    'count' => $item->count(),
                    'user' => $item->pluck('user_id')->contains($authenticatedUser->id),
                ];
            })->values()->toArray();

            // Include comment reactions within the comment object
            $comment->reactionsData = $commentReactions;

            // Process reactions for comment replies
            foreach ($comment->replies as $reply) {
                $replyReactions = $reply->reactions->groupBy('reaction.title')->map(function ($item) use ($authenticatedUser) {
                    return [
                        'title' => $item->first()->reaction->title,
                        'count' => $item->count(),
                        'user' => $item->pluck('user_id')->contains($authenticatedUser->id),
                    ];
                })->values()->toArray();
                $reply->reactionsData = $replyReactions;
            }
        }


        return  response()->json(['post' => $post]);
    }



}

