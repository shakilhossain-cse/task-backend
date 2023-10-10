<?php

namespace App\Http\Controllers;

use App\Events\NewNotificationEvent;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Replie;
use App\Models\UserReaction;
use Illuminate\Http\Request;

class UserReactionController extends Controller
{

    public function allreactions()  {

       $reactions =  Reaction::get();
      return response()->json($reactions);

    }
    public function reactToPost(Request $request, Post $post, $reactionId)
    {
        return $this->react($request, $post, $reactionId);
    }

    public function reactToComment(Request $request, Comment $comment, $reactionId)
    {
        return $this->react($request, $comment, $reactionId);
    }

    public function reactToReply(Request $request, Replie $reply, $reactionId)
    {
        return $this->react($request, $reply, $reactionId);
    }

    private function react(Request $request, $model, $reactionId)
    {
        $user = auth()->user();


        $existingReaction = UserReaction::where([
            'user_id' => $user->id,
            'reactable_id' => $model->id,
            'reactable_type' => get_class($model),
        ])->first();

        if ($existingReaction) {
            if ($existingReaction->reaction_id == $reactionId) {
                $existingReaction->delete();
                return response()->json(['message' => 'Reaction removed successfully.']);
            } else {
                $existingReaction->update(['reaction_id' => $reactionId]);
                return response()->json(['message' => 'Reaction updated successfully.', 'reaction' => $existingReaction]);
            }
        }

        $reaction = $user->reactions()->create([
            'reaction_id' => $reactionId,
            'reactable_id' => $model->id,
            'reactable_type' => get_class($model),
        ]);

        $reactableOwner = $model->user; 
        $notificationMessage = "New reaction on your {$model->getMorphClass()}";

        $notification = $reactableOwner->notifications()->create([
            'notifiable_id' => $model->id,
            'notifiable_type' => get_class($model),
            'data' => json_encode(['message' => $notificationMessage, 'reaction_id' => $reaction->id]),
            'read' => false,
        ]);

        // Dispatch the NewNotificationEvent
        event(new NewNotificationEvent($notification));


        return response()->json(['message' => 'Reaction added successfully.', 'reaction' => $reaction]);
    }

}
