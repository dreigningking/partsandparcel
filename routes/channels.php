<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{id}', function ($user, $id) {
    if (! $user) {
        return false;
    }

    return \App\Models\ConversationParticipant::where('conversation_id', $id)
        ->where('user_id', $user->id)
        ->exists();
});
