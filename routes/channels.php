<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Public channel voor match updates - iedereen kan luisteren
Broadcast::channel('matches', function () {
    return true;
});
