<?php

use App\Status\UserType;
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


Broadcast::channel('onGoingOrders', function ($user) {
    return $user->type === UserType::WAITER
        || $user->type === UserType::CASHER
        || $user->type === UserType::KITCHEN;
});

Broadcast::channel('readyToDeliver', function ($user) {
    return $user->type === UserType::WAITER
        || $user->type === UserType::CASHER
        || $user->type === UserType::KITCHEN;
});


Broadcast::channel('newOrder', function ($user) {
    return $user->type === UserType::WAITER
        || $user->type === UserType::CASHER
        || $user->type === UserType::KITCHEN;
});


Broadcast::channel('WaitingOrder', function ($user) {
    return $user->type === UserType::KITCHEN;
});

Broadcast::channel('PastOrders', function ($user) {
    return $user->type === UserType::CASHER;
});
