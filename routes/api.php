<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\UserReactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register',[AuthController::class, 'register']);
Route::post('/login', [AuthController::class,'login']);;



// Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/create', [PostController::class, 'store']);
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/post/{id}', [PostController::class, 'show']);
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::post('/comments/{comment}/replies', [ReplyController::class, 'store']);
    Route::get('/reactions', [UserReactionController::class, 'allreactions']);

    Route::post('/posts/{post}/react/{reactionId}', [UserReactionController::class, 'reactToPost']);
    Route::post('/comments/{comment}/react/{reactionId}', [UserReactionController::class, 'reactToComment']);
    Route::post('/replies/{reply}/react/{reactionId}', [UserReactionController::class, 'reactToReply']);



    Route::get('/notifications', [NotificationController::class, 'index']);
});
