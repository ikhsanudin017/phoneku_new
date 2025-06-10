<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Di file ini kamu bisa register semua channel yang aplikasi kamu gunakan.
| Channel broadcasting akan dipakai buat fitur realtime, kayak chat, notifikasi, dll.
|
*/

Broadcast::channel('private-chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId || $user->role === 'admin';
});
