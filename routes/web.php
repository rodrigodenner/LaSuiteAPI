<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return response()->json([
    'message' => 'BEM VINDO A API La Suite '
  ]);
});
