<?php

use Illuminate\Support\Facades\Broadcast;

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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('admin', function ($user) {
    return $user->role === 'admin' || $user->is_admin;
});

Broadcast::channel('courier.{code}', function ($user, $code) {
    return $user->role === 'courier' && $user->courierAccount && $user->courierAccount->code === $code;
});

Broadcast::channel('rider.{riderId}', function ($user, $riderId) {
    return $user->role === 'rider' && $user->rider && (int) $user->rider->id === (int) $riderId;
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
