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
    request()->validate([
        'name'=>'required',
    ]);
    $name=request()->get('name');
    $user=User::where('name','=', $name)->pluck('id');
    if(!$user->isEmpty()){
        $user=User::findOrFail($user[0]);
        return($user);
    }
    else
        return User::create([
            'name'=>request('name')
        ]);
});

Route::get('/posts',function (){
    return post::all();
});
Route::post('/posts',function (){
    $name=request()->get('username');
    $url=request()->get('url');
    $checkurl=True;
    $userid=User::where('name','=', $name)->pluck('id');
    $urls = post::where('userid','=',$userid[0])->pluck('url');
    request()->validate([
        'url'=>'required',
        'status'=>'required',
    ]);
    foreach ($urls as $i) {
        if($i==$url){
            $checkurl=false;
        }
    }
    if($checkurl==true){
        return post::create([
            'userid'=>$userid[0],
            'url'=>request('url'),
            'status'=>request('status'),
        ]);
    }
    else
        return ("same url");
});
Route::get('/posts/{username}',function (){
    $name=request()->get('name');
    $userid=User::where('name','=', $name)->pluck('id');
    $posts=post::where('userid','=',$userid[0])->pluck('url');
    dd($posts);
    return($posts);
});

