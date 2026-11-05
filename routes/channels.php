<?php

use Illuminate\Support\Facades\Broadcast;

// Default Laravel model channel (keep for web features).
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private channel used by LoginAttemptBroadcast.
// The mobile app subscribes as: private-user.{userId}
// Authorization: the authenticated user's id must match the channel suffix.
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
