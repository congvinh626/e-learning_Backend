<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

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

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

// Broadcast::channel('presence-chat', function ($user, $userId) {
//     echo(1111);
//     return true;
//     // return $user->id === $userId;
//   });
Broadcast::channel('presence-chat', function ($user, $userId) {
  // if ($user->id === $userId) {
  //   return array('name' => $user->name);
  // }
  return true;
});
// Broadcast::channel('room2', function ($user, $id) {
//     return true; // user có thể join vào bất kì chatroom nào
// });