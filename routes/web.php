<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});




Route::middleware(
    [ 'auth:sanctum', config('jetstream.auth_session'), 'verified' ]
)->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::group(['middleware' =>
        ['permission:user list | create user | edit user|delete user |role list | create role |
        edit role | delete role']], function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);

    });


});
