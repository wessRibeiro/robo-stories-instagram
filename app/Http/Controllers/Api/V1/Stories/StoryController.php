<?php

namespace Louder\Http\Controllers\Api\V1\Stories;

use Illuminate\Http\Request;
use Louder\Http\Controllers\Controller;
use Louder\Services\Api\V1\StoryService;

class StoryController extends Controller
{
    protected $_service;
    protected $_request;

    function __construct(StoryService $service, Request $request)
    {
        $this->_service = $service;
        $this->_request = $request;
    }

    function index()
    {
        $dados = [
            'code'      => 200,
            'message'   => 'Ok',
            'data'      => $this->_service->index()
        ];

        return response()->json($dados, $dados['code'])->withHeaders([
            'Content-Type'  => 'application/json; charset=utf-8',
            'Cache-Control' => 'public'
        ]);
    }

    public function approve($program, $instagramStoryId)
    {
        $apiResponse = $this->_service->approve($instagramStoryId);
        return response()->json($apiResponse, $apiResponse['code'])->withHeaders([
            'Content-Type'  => 'application/json; charset=utf-8',
            'Cache-Control' => 'public'
        ]);
    }

    public function disapprove($program, $instagramStoryId)
    {
        $apiResponse = $this->_service->disapprove($instagramStoryId);
        return response()->json($apiResponse, $apiResponse['code'])
                         ->withHeaders([
                                            'Content-Type'  => 'application/json; charset=utf-8',
                                            'Cache-Control' => 'public'
                                       ]);
    }

}
