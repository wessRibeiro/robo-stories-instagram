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
use Louder\Models\V1\AnalyticsInfluencer;
use Louder\Models\V1\Analytics;
use Louder\Models\V1\Story;

class GraphicWeeklyImpactService
{
    protected $_influencerModel;
    protected $_analyticsInfluencerModel;
    protected $_analyticsModel;
    protected $_storyModel;
    protected $_router;
    protected $_request;

    public function __construct(Router              $router,
                                Influencer          $influencerModel,
                                AnalyticsInfluencer $analyticsinfluencerModel,
                                Analytics           $analyticsModel,
                                Story               $storyModel,
                                Request             $request)
    {
        $this->_influencerModel             = $influencerModel;
        $this->_analyticsInfluencerModel    = $analyticsinfluencerModel;
        $this->_analyticsModel              = $analyticsModel;
        $this->_storyModel                  = $storyModel;
        $this->_router                      = $router;
        $this->_request                     = $request;
    }

    public function index($qtd = 7){
        
        $dateTarget = date('Y-m-d', strtotime("-" . ($qtd - 1) . " day"));
        $dateNow = date('Y-m-d');
        $dateIntersec = '';
        $postsTotal = 0;
        $seguidoresTemp = 0;


        $seguidores = collect($this->_analyticsInfluencerModel->select(DB::raw('data, SUM(seguidores) as seguidores'))
                                                              ->groupBy('data')
                                                              ->pluck('seguidores', 'data'));
                                                                
        $posts = collect($this->_analyticsModel->select(DB::raw('data, SUM(postsHashtag) as postsHashtag'))
                                               ->where('postsHashtag', '>', '0')
                                               ->groupBy('data')
                                               ->pluck('postsHashtag', 'data'));

        $dateIntersec = (key(current($posts)) < key(current($seguidores))) ? key(current($posts)) : key(current($seguidores));

        while($dateIntersec <= $dateNow){
            
            $postsTotal += $posts[$dateIntersec] ?? $postsTotal;
            $seguidoresTemp = $seguidores[$dateIntersec] ?? $seguidoresTemp;
            
            if($dateIntersec >= $dateTarget){
                $dados[$dateIntersec]['value'] = round(($postsTotal * $seguidoresTemp * 4) / 100, 0);
                $dados[$dateIntersec]['label'] = ($dateIntersec == $dateNow) ? 'Hoje': getDiaSemana($dateIntersec);
            }

            $dateIntersec = date('Y-m-d', strtotime("+1 day", strtotime($dateIntersec)));
        }

        $return['labels'] = array_column($dados, 'label');
        $return['datasets']['impact']['label'] = 'Impacto (Ultimos ' . $qtd . ' dias)';
        $return['datasets']['data']  = array_column($dados, 'value');
        
        return $return;
    }
}