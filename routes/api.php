<?php

use App\Models\post;
use App\Models\User;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/users',function (){
    return \App\Models\User::all();
});
Route::post('/users',function (){
    $user=0;
    request()->validate([
        'name'=>'required',
    ]);
    $name=request()->get('name');
    if($user[0]!=''){
        dd(123);
    }
    dd(456);

    return User::create([
        'name'=>request('name')
    ]);
});

Route::get('/posts',function (){
    return post::all();
});
Route::post('/posts',function (){
    request()->validate([
        'userid'=>'required',
        'url'=>'required',
        'status'=>'required',
        'type'=>'required',
    ]);
    return post::create([
        'userid'=>request('userid'),
        'url'=>request('url'),
        'status'=>request('status'),
        'type'=>request('type'),
    ]);
});
