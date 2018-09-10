<?php

namespace Louder\Http\Controllers\Api\V1\Dashboard;

use Illuminate\Http\Request;
use Louder\Http\Controllers\Controller;
use Louder\Services\Api\V1\GraphicService;


class GraphicController extends Controller
{
    protected $_service;
    protected $_request;

    function __construct(GraphicService $service, Request $request)
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

        return response()->json($dados, $dados['code'])
                         ->withHeaders([
                                            'Content-Type'  => 'application/json; charset=utf-8',
                                            'Cache-Control' => 'public'
                                       ]);
    }
}
