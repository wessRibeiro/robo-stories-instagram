<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04/09/2018
 * Time: 15:11
 */

namespace Louder\Services\Api\V1\Graphics;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;
use Louder\Models\V1\Influencer;
use Louder\Models\V1\Analytics;
use Carbon\Carbon;

class GraphicFeedService
{
    protected $_influencerModel;
    protected $_analyticsModel;
    protected $_router;
    protected $_request;
    protected $_carbon;

    public function __construct(Router          $router,
                                Influencer      $influencerModel,
                                Analytics       $analyticsModel,
                                Carbon          $carbon,
                                Request         $request)
    {
        $this->_influencerModel  = $influencerModel;
        $this->_analyticsModel   = $analyticsModel;
        $this->_router           = $router;
        $this->_carbon           = $carbon;
        $this->_request          = $request;
    }

    public function index(){

        $return['labels'] = mesesAcronimo();
        $return['datasets']['posts']['label'] = 'Posts';
        //dados do ano atual
        $dataAnalytics = collect($this->_analyticsModel->where('data', '>=', $this->_carbon->format('Y'))
                                                       ->orderBy('data')
                                                       ->get()
                                 );

        //inicializando
        foreach (meses() as $key => $mes) {
            $mothHasAnalytics[$key]['comments'] = [];
            $mothHasAnalytics[$key]['posts']    = [];
            $mothHasAnalytics[$key]['likes']    = [];
        }

        //posicionando dados em suas datas especificas
        foreach($dataAnalytics as $analytic){
            $dataAnalytic = $this->_carbon->createFromFormat('Y-m-d', $analytic->data);
            array_push($mothHasAnalytics[$dataAnalytic->format('n')]['comments'], $analytic->comentariosHashtag);
            array_push($mothHasAnalytics[$dataAnalytic->format('n')]['posts'], $analytic->postsHashtag);
            array_push($mothHasAnalytics[$dataAnalytic->format('n')]['likes'], $analytic->likesHashtag);
        }
        //formatando para o gráfico
        $posts = [
            'label'             => 'Posts',
            'backgroundColor'   => 'rgba(9,35,251,0.3)',
            'data'              =>  []
        ];

        $comments = [
            'label'             => 'Comentários',
            'backgroundColor'   => 'rgba(248,109,32,0.3)',
            'data'              =>  []
        ];

        $likes = [
            'label'             => 'Likes',
            'backgroundColor'   => 'rgba(255,158,0,0.3)',
            'data'              =>  []
        ];

        //somando posições
        foreach (meses() as $key => $mes) {
            array_push($comments['data'], collect($mothHasAnalytics[$key]['comments'])->sum());
            array_push($posts['data'], collect($mothHasAnalytics[$key]['posts'])->sum());
            array_push($likes['data'], collect($mothHasAnalytics[$key]['likes'])->sum());
        }


        array_push($return['datasets'], $posts);
        array_push($return['datasets'], $comments);
        array_push($return['datasets'], $likes);


        return $return;
    }
}