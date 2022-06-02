<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'post'], function () {
    Route::get('', [PostController::class, 'index']);
    Route::post('/', [PostController::class, 'store']);
    Route::put('/{id}', [PostController::class, 'update']);
    Route::get('/{id}', [PostController::class, 'show']);
    Route::delete('/{id}', [PostController::class, 'destroy']);
});
Route::group(['prefix' => 'attachment'], function () {
    Route::get('', [AttachmentController::class, 'index']);
    Route::post('/', [AttachmentController::class, 'store']);
    Route::put('/{id}', [AttachmentController::class, 'update']);
    Route::get('/{id}', [AttachmentController::class, 'show']);
    Route::delete('/{id}', [AttachmentController::class, 'destroy']);
});
Route::group(['prefix' => 'comment'], function () {
    Route::get('', [CommentController::class, 'index']);
    Route::post('/', [CommentController::class, 'store']);
    Route::put('/{id}', [CommentController::class, 'update']);
    Route::get('/{id}', [CommentController::class, 'show']);
    Route::delete('/{id}', [CommentController::class, 'destroy']);
});
Route::group(['prefix' => 'user'], function () {
    Route::get('', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::group(['prefix' => 'tag'], function () {
    Route::get('', [TagController::class, 'index']);
    Route::post('/', [TagController::class, 'store']);
    Route::put('/{id}', [TagController::class, 'update']);
    Route::get('/{id}', [TagController::class, 'show']);
    Route::delete('/{id}', [TagController::class, 'destroy']);
});
