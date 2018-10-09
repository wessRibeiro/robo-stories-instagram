<?php

namespace Louder\Http\Controllers\Api\V1\Stories;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Louder\Http\Controllers\Controller;
use Louder\Services\Api\V1\StoryService;
use Louder\Models\V1\Influencer;

class StoryController extends Controller
{
    protected $_service;
    protected $_request;
    protected $_route;

    function __construct(StoryService $service, Request $request, Route $_route)
    {
        $this->_request = $request;
        $this->_route   = $_route;
        $this->_service = $service;
    }

    function index()
    {
        $dados = [
            'code'        => 200,
            'message'     => 'Ok',
            'data'        => $this->_service->index(),
            'influencers' => Influencer::where('ativo', '=', 1)->get()
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
