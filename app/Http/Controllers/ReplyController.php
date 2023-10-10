<?php

namespace App\Http\Controllers;

use App\Events\NewNotificationEvent;
use App\Http\Requests\ReplyRequest;
use App\Models\Comment;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function store(ReplyRequest $request, Comment $comment)
    {
        $reply = $comment->replies()->create(['body' => $request->body , 'user_id' => auth()->id()]);

        $commentOwnerNotification = $comment->user->notifications()->create([
            'notifiable_id' => $reply->id,
            'notifiable_type' => Reply::class,
            'data' => json_encode(['message' => 'New reply on your comment', 'reply_id' => $reply->id]),
            'read' => false,
        ]);


        $postOwnerNotification = $comment->post->user->notifications()->create([
            'notifiable_id' => $reply->id,
            'notifiable_type' => Reply::class,
            'data' => json_encode(['message' => 'New reply on a comment on your post', 'reply_id' => $reply->id]),
            'read' => false,
        ]);

        event(new NewNotificationEvent($commentOwnerNotification));
        event(new NewNotificationEvent($postOwnerNotification));
        return response()->json($reply, 201);
    }
}
