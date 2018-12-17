<?php

use Illuminate\Http\Request;

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

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

//V1
Route::group(['prefix' => 'v1'], function() {
    //PROGRAM
    Route::group(['prefix' => '{program}'], function() {
        //DASHBOARD
        Route::group(['prefix' => 'dashboard'], function(){
            Route::group(['prefix' => 'graphics'], function() {
                Route::get('/', [
                    'as' => 'dashboard.graphics',
                    'uses' => 'Api\V1\Dashboard\GraphicController@index'
                ]);

                /*Route::post('/', [
                    'as' => 'tipo.acao.create',
                    'uses' => 'Api\V1\TipoAcaoController@create'
                ]);

                Route::get('/{id}', [
                    'as' => 'tipo.acao.find',
                    'uses' => 'Api\V1\TipoAcaoController@find'
                ]);

                Route::put('/{id}', [
                    'as' => 'tipo.acao.update',
                    'uses' => 'Api\V1\TipoAcaoController@update'
                ]);

                Route::delete('/{id}', [
                    'as' => 'tipo.acao.delete',
                    'uses' => 'Api\V1\TipoAcaoController@delete'
                ]);

                Route::patch('/{id}', [
                    'as' => 'tipo.acao.restore',
                    'uses' => 'Api\V1\TipoAcaoController@restore'
                ]);*/
            });
        });
        //STORIES
        Route::group(['prefix' => 'stories'], function(){
            Route::get('/', [
                'as' => 'stories',
                'uses' => 'Api\V1\Stories\StoryController@index'
            ]);
            Route::patch('/{instagramStoryId}/approving/{missao}', [
                'as' => 'stories.approve',
                'uses' => 'Api\V1\Stories\StoryController@approve'
            ]);
            Route::patch('/{instagramStoryId}/disapproving', [
                'as' => 'stories.disapprove',
                'uses' => 'Api\V1\Stories\StoryController@disapprove'
            ]);

            /*Route::post('/', [
            'as' => 'tipo.acao.create',
            'uses' => 'Api\V1\TipoAcaoController@create'
        ]);

        Route::get('/{id}', [
            'as' => 'tipo.acao.find',
            'uses' => 'Api\V1\TipoAcaoController@find'
        ]);

        Route::put('/{id}', [
            'as' => 'tipo.acao.update',
            'uses' => 'Api\V1\TipoAcaoController@update'
        ]);

        Route::delete('/{id}', [
            'as' => 'tipo.acao.delete',
            'uses' => 'Api\V1\TipoAcaoController@delete'
        ]);

        */
        });
    });
});
