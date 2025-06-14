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

// Channel for admins to receive messages
Broadcast::channel('private-admin.chat', function ($user) {
    return $user->role === 'admin';
});

// Channel for customers to receive messages
Broadcast::channel('private-customer.{customerId}', function ($user, $customerId) {
    // Allow if user is the customer, or if user is an admin
    return $user->role === 'admin' || (int) $user->id === (int) $customerId;
});
