<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CollaborationController;
use App\Http\Controllers\Notes;
use App\Http\Controllers\NotesCategoriesController;
use App\Http\Controllers\NotesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function(){
    Route::get('/user', function (Request $request) {
        return response([
            'status' => 'success',
            'user' => $request->user(),
        ], 200);
    });

    // routes for notes
    Route::post('/notes', [NotesController::class, 'store']);
    Route::get('/notes', [NotesController::class, 'index']);
    Route::get('/notes/{noteId}', [NotesController::class, 'show']);
    Route::put('/notes/{noteId}', [NotesController::class, 'update']);
    Route::delete('notes/{noteId}', [NotesController::class, 'destroy']);

    // routes for categories
    Route::post('/categories', [CategoriesController::class, 'store']);
    Route::get('/categories', [CategoriesController::class, 'index']);
    Route::get('/categories/{categoryId}', [CategoriesController::class, 'show']);
    Route::put('/categories/{categoryId}', [CategoriesController::class, 'update']);
    Route::delete('/categories/{categoryId}', [CategoriesController::class, 'destroy']);

    // routes for notes_in_categories
    Route::post('/categories/{categoryId}/notes', [NotesCategoriesController::class, 'store']);
    Route::get('/categories/{categoryId}/notes', [NotesCategoriesController::class, 'index']);
    Route::get('/categories/{categoryId}/notes/{noteId}', [NotesCategoriesController::class, 'show']);
    Route::delete('/categories/{categoryId}/notes/{noteId}', [NotesCategoriesController::class, 'destroy']);

    //routes for collaboration
    Route::get('/collaboration', [CollaborationController::class, 'index']);
    Route::get('/collaboration/{categoryId}/users', [CollaborationController::class, 'show']);
    Route::post('/collaboration/{categoryId}', [CollaborationController::class, 'store']);
    Route::delete('/collaboration/{categoryId}', [CollaborationController::class, 'destroy']);
    Route::delete('/collaboration/{categoryId}/users', [CollaborationController::class, 'deleteAllUsersFromCategory']);

    // routes to post new note and update existing note in categories. For collaboration users
    // Route::post('/categories/{categoryId}/note', [NotesCategoriesController::class, 'storeNote']);
    // Route::put('/categories/{categoryId}/note/{noteId}', [NotesCategoriesController::class, 'updateNote']);

    // route to log out user
    Route::post('/logout', [AuthController::class, 'logout']);
});
