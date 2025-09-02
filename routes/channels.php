<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Channel untuk notifikasi pengguna
Broadcast::channel('user.notifications.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel untuk notifikasi tugas
Broadcast::channel('task.{id}', function ($user, $id) {
    return true; // Pengguna yang terotentikasi dapat melihat pembaruan tugas
});
