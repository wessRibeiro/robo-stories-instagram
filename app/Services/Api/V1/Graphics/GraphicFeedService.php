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
use Louder\Models\V1\Influencer;
use Louder\Models\V1\Story;
use Louder\Models\V1\Analytics;
use Carbon\Carbon;

class GraphicFeedService
{
    protected $_influencerModel;
    protected $_storyModel;
    protected $_analyticsModel;
    protected $_router;
    protected $_request;
    protected $_carbon;

    public function __construct(Router          $router,
                                Influencer      $influencerModel,
                                Story           $storyModel,
                                Analytics       $analyticsModel,
                                Carbon          $carbon,
                                Request         $request)
    {
        $this->_influencerModel  = $influencerModel;
        $this->_storyModel       = $storyModel;
        $this->_analyticsModel   = $analyticsModel;
        $this->_router           = $router;
        $this->_carbon           = $carbon;
        $this->_request          = $request;
    }

    public function index($program){

        $return['labels'] = mesesAcronimo();

        //dados do ano atual
        $dateAnalytics = collect($this->_analyticsModel->setConnection($program)
                                                       ->where('data', '>=', $this->_carbon->format('Y'))
                                                       ->orderBy('data')
                                                       ->get()
                                 );

        //dados do ano atual stories
        $dataStories = collect($this->_storyModel->setConnection($program)
                                                 ->where('vinculadoem', '>=', $this->_carbon->format('Y'))
                                                 ->where('temhashtag', '=', 1)
                                                 ->orderBy('vinculadoem')
                                                 ->get()
                             );

        //inicializando
        foreach (meses() as $key => $mes) {
            $mothHasAnalytics[$key]['comments'] = [];
            $mothHasAnalytics[$key]['posts']    = [];
            $mothHasAnalytics[$key]['likes']    = [];
            //stories
            $mothHasStories[$key]               = 0;
        }

        //posicionando dados em suas datas especificas
        foreach($dateAnalytics as $analytic){
            $dateAnalytic = $this->_carbon->createFromFormat('Y-m-d', $analytic->data);
            array_push($mothHasAnalytics[$dateAnalytic->format('n')]['comments'], $analytic->comentariosHashtag);
            array_push($mothHasAnalytics[$dateAnalytic->format('n')]['posts'], $analytic->postsHashtag);
            array_push($mothHasAnalytics[$dateAnalytic->format('n')]['likes'], $analytic->likesHashtag);
        }
        
        //posicionando dados em suas datas especificas stories
        foreach($dataStories as $Story){
            $dateStory = $this->_carbon->createFromFormat('Y-m-d', date('Y-m-d', strtotime($Story->vinculadoem)));
            $mothHasStories[$dateStory->format('n')]++;

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

        $stories = [
            'label'             => 'Stories',
            'backgroundColor'   => 'rgba(102,51,153,0.3)',
            'data'              =>  []
        ];

        //somando posições
        foreach (meses() as $key => $mes) {
            array_push($comments['data'], collect($mothHasAnalytics[$key]['comments'])->sum());
            array_push($posts['data'], collect($mothHasAnalytics[$key]['posts'])->sum());
            array_push($likes['data'], collect($mothHasAnalytics[$key]['likes'])->sum());
            array_push($stories['data'],$mothHasStories[$key]);
        }

        $return['datasets'] = [$posts, $comments, $likes, $stories];


        return $return;
    }
}