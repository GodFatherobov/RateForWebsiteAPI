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
        $urls=rate::all()->pluck('url');
        $checkurl=True;
        foreach ($urls as $i) {
            if($i==$url){
                $rates=rate::where('url',$url)->pluck('id');
                $rate=rate::findOrFail($rates[0]);
                $likecount=rate::where('url',$url)->pluck('likecount');
                $dislikecount=rate::where('url',$url)->pluck('dislikecount');
                if($status=='like'){
                    $likecount[0]=$likecount[0]+1;

                }
                else
                    $dislikecount[0]=$dislikecount[0]+1;
                $result=100/($likecount[0]+$dislikecount[0])*$likecount[0];
                $checkurl=False;
                $data=([
                    'url'=>$url,
                    'likecount'=>$likecount[0],
                    'dislikecount'=>$dislikecount[0],
                    'rate'=>$result,
                ]);
                $rate->update($data);
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
        return post::create([
            'userid'=>$userid[0],
            'url'=>request('url'),
            'status'=>request('status'),
        ]);
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
Route::get('/posts/delete/{username}/{url}',function ($username,$url){
    $userid=User::where('name','=', $username)->pluck('id');
    $status=post::where('userid','=',$userid[0])->where('url',$url)->pluck('status');
    $likecount=rate::where('url',$url)->pluck('likecount');
    $dislikecount=rate::where('url',$url)->pluck('dislikecount');
    $rate=0;
    if($status[0]=='like')
    {
        $likecount[0] = $likecount[0] - 1;
        if(($likecount[0]+$dislikecount[0])!=0) {
            $rate = 100 / ($likecount[0] + $dislikecount[0]) * $likecount[0];
        }
        rate::where('url', '=', $url)->update(array('likecount' => $likecount[0], 'rate' => $rate));
    }
    else{
        $dislikecount[0]=$dislikecount[0]+1;
        if(($likecount[0]+$dislikecount[0])!=0) {
            $rate = 100 / ($likecount[0] + $dislikecount[0]) * $likecount[0];
        }
        rate::where('url','=',$url)->update(array('dislikecount' => $dislikecount[0],'rate'=>$rate));
    }
    post::where('userid','=',$userid[0])->where('url',$url)->delete();
    return(post::all());
});

Route::get('/rates',function (){
    return(rate::all());
});
Route::get('/rates/{require}/{percent}/{upordown}',function ($require,$percent,$upordown){
    $urls=Rate::all()->pluck('url');
    $result=[];
    foreach ($urls as $url){
        if(Str :: contains ( $url , $require)){
            $data[]=$url;
        }
    }
    if($upordown=='up') {
        foreach ($data as $datum) {
            $picks[] = rate::where('url', $datum)->where('rate', '>=', $percent)->first();
        }
    }
    else {
        foreach ($data as $datum) {
            $picks[]= rate::where('url', $datum)->where('rate', '<=', $percent)->first();
        }
    }
    foreach($picks as $pick) {
        if ($pick != null) {
            $result[] = $pick;
        }
    }
    return($result);
});


