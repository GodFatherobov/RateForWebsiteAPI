<?php

use App\Models\post;
use App\Models\rate;
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
    $status=request()->get('status');
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
        post::create([
            'userid'=>$userid[0],
            'url'=>request('url'),
            'status'=>request('status'),
        ]);
        $urls=rate::all()->pluck('url');
        $checkurl=True;
        foreach ($urls as $rurl) {
            if($rurl==$url){
                $rate=rate::where('url',$url)->get();
                $checkurl=False;
                dd($rate);
            }
        }
        if($checkurl==True){
            if($status=='like'){
                rate::create([
                    'url'=>$url,
                    'likecount'=>1,
                    'dislikecount'=>0,
                    'rate'=>100,
                ]);
            }
            else
                rate::create([
                    'url'=>$url,
                    'likecount'=>0,
                    'dislikecount'=>1,
                    'rate'=>0,
                ]);
        }
        return('create success');
    }
    else
        return ("same url");
});
Route::get('/posts/{username}',function ($username){
    $userid=User::where('name','=', $username)->pluck('id');
    $posts=post::where('userid','=',$userid[0])->where('status','like')->get();
    return($posts);
});
Route::get('/dislikeposts/{username}',function ($username){
    $userid=User::where('name','=', $username)->pluck('id');
    $posts=post::where('userid','=',$userid[0])->where('status','dislike')->get();
    return($posts);
});

Route::get('/rates',function (){
    return(rate::all());
});
Route::get('/rates/{require}',function ($require){
    return(rate::all());
});


