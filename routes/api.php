<?php

use App\Http\Controllers\Api\{
    UserController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::resource('users', UserController::class);