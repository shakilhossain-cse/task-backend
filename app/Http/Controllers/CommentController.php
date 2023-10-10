<?php

namespace App\Http\Controllers;

use App\Events\NewNotificationEvent;
use App\Http\Requests\CommentRequest;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Post $post)
    {
        $comment = $post->comments()->create(['body' => $request->body, 'user_id' => auth()->id()]);

        $commentOwnerNotification = auth()->user()->notifications()->create([
            'notifiable_id' => $comment->id,
            'notifiable_type' => Comment::class,
            'data' => json_encode(['message' => 'New comment added', 'comment_id' => $comment->id]),
            'read' => false,
        ]);

        // Create a notification for the post owner
        $postOwnerNotification = $post->user->notifications()->create([
            'notifiable_id' => $comment->id, // You might want to use a different ID here
            'notifiable_type' => Comment::class,
            'data' => json_encode(['message' => 'New comment on your post', 'comment_id' => $comment->id]),
            'read' => false,
        ]);

        // Dispatch the events
        event(new NewNotificationEvent($commentOwnerNotification));
        event(new NewNotificationEvent($postOwnerNotification));
        
        return response()->json($comment, 201);
    }
}