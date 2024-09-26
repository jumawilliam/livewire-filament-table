<?php

use App\Livewire\Users\ListUsers;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('users', ListUsers::class);
