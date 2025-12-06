<?php

use Illuminate\Support\Facades\Route;

// This is the ONLY route you need.
// It loads your Single Page App (scheduler.blade.php).
Route::get('/', function () {
    return view('scheduler');
});