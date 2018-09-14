<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04/09/2018
 * Time: 15:11
 */

namespace Louder\Services\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;
use Louder\Models\V1\Influencer;
use Louder\Models\V1\Analytics;
use Louder\Models\V1\Story;
use Louder\Services\Api\V1\Graphics\GraphicFeedService;
use Louder\Services\Api\V1\Graphics\GraphicWeeklyImpactService;

class GraphicService
{
    protected $_influencerModel;
    protected $_graphicFeedService;
    protected $_graphicWeeklyImpactService;
    protected $_analyticsModel;
    protected $_storyModel;
    protected $_router;
    protected $_request;

    public function __construct(Router                      $router,
                                Influencer                  $influencerModel,
                                Story                       $storyModel,
                                GraphicFeedService          $graphicFeedService,
                                GraphicWeeklyImpactService  $graphicWeeklyImpactService,
                                Analytics                   $analyticsModel,
                                Request                     $request)
    {
        $this->_influencerModel             = $influencerModel;
        $this->_storyModel                  = $storyModel;
        $this->_graphicFeedService          = $graphicFeedService;
        $this->_graphicWeeklyImpactService  = $graphicWeeklyImpactService;
        $this->_analyticsModel              = $analyticsModel;
        $this->_router                      = $router;
        $this->_request                     = $request;
    }

    public function index(){
        $universe = [
            'totalActiveInfluencers'    => $this->_influencerModel->where('ativo', true)->count(),
            'totalStoriesHashtag'       => $this->_storyModel->where('temhashtag', true)->count(),
            'sumLikesToday'             => collect($this->_analyticsModel->pluck('likesHoje'))->sum(),
            'sumPostsToday'             => collect($this->_analyticsModel->pluck('postsHoje'))->sum(),
            'sumCommentsToday'          => collect($this->_analyticsModel->pluck('comentariosHoje'))->sum(),
            'sumLikesHashtag'           => collect($this->_analyticsModel->pluck('likesHashtag'))->sum(),
            'sumPostsHashtag'           => collect($this->_analyticsModel->pluck('postsHashtag'))->sum(),
            'sumCommentsHashtag'        => collect($this->_analyticsModel->pluck('comentariosHashtag'))->sum(),
            'universeHashtag'           => collect($this->_analyticsModel->pluck('universoHashtag'))->pop(),

        ];

        $universe['postsPercent']    = number_format(($universe['sumPostsHashtag']*100)/$universe['sumPostsToday'], 2,',','.')."%";
        $universe['commentsPercent'] = number_format(($universe['sumCommentsHashtag']*100)/$universe['sumCommentsToday'], 2,',','.')."%";
        $universe["likesPercent"]    = number_format(($universe['sumLikesHashtag']*100)/$universe['sumLikesToday'], 2,',','.')."%";
        $universe["universePercent"] = number_format(($universe['sumPostsHashtag']*100)/$universe["universeHashtag"], 2,',','.')."%";
        $universe['totalFollowers']  = collect();

        foreach ($this->_influencerModel->all() as $influencer){
            $universe['totalFollowers']->push($influencer->profile->followed_by);
        }
        $universe['totalFollowers']  = $universe['totalFollowers']->sum();
        //$universe['totalFollowers']  = number_format((int)$universe['totalFollowers']->sum(), 2,',','.');
        $universe['engagement']      = round(($universe['sumLikesHashtag'] + $universe['sumCommentsHashtag'])/$universe['totalFollowers'], 2)."%";
        $universe['impact']          = round((($universe['sumPostsHashtag'] * $universe['totalFollowers']) * 4)/100,0 );

        $sqlTop5Influencers = 'SELECT 	
                                    per.idInfluencer,
                                    per.profile_pic_url_hd  imagem_usuario,
                                    per.full_name           nome_completo,
                                    per.username,
                                    per.followed_by         num_seguidores,
                                    Count(p.id)             num_posts,
                                    SUM(p.likes)            num_likes,
                                    SUM(p.comentarios)      num_comentarios,
                                    (SUM(p.likes) + SUM(p.comentarios) + Count(p.id)) total
                                FROM 
                                    Perfis per
                                LEFT JOIN 
                                    Posts p
                                ON 
                                    p.owner_id = per.idInfluencer
                                LEFT JOIN 
                                    Posts_Curadoria pc
                                ON 
                                    pc.idPost = p.code
                                WHERE 
                                    pc.aprovado = 1
                                GROUP BY 
                                    per.idInfluencer
                                ORDER BY 
                                    total 
                                DESC
                                LIMIT 5';

        $resultTop5Influencers = DB::select(DB::raw($sqlTop5Influencers));
        $universe['top5Influencers'] = json_decode(json_encode($resultTop5Influencers), true);

        $sqlTop5Ranking = 'SELECT 
                              per.id,
                              per.profile_pic_url imagem,
                              per.username,
                              per.full_name,
                              (
                                (SUM(p.likes))
                                +
                                3 * (SUM(p.comentarios))
                                +
                                300 * (COUNT(pc.id))
                                +
                                100 * IFNULL((SELECT COUNT(h.id) FROM Historias h WHERE h.iduser = (SELECT i.id FROM Influencers i WHERE i.instagram = per.username) ),0)
                              ) pontos ,
                              per.followed_by seguidores,
                            (COUNT(pc.id)) posts,
                            (SELECT SUM(pr.pontos) FROM PremiosResgatados pr WHERE pr.idInfluencer IN (SELECT DISTINCT i.id FROM Influencers i WHERE i.idInstagram = per.idInfluencer)) resgatados,
                             SUM(p.comentarios) comentarios,
                            (SELECT COUNT(inf.id) FROM Influencers inf WHERE inf.ativo = "1") total_influencers,
                                  IFNULL((SELECT COUNT(h.id) FROM Historias h WHERE h.iduser = (SELECT i.id FROM Influencers i WHERE i.instagram = per.username) ),0) historias
            
                        FROM 
                          Posts_Curadoria pc
                        JOIN 
                          Posts p ON p.code = pc.idPost
                        JOIN 
                          Perfis per ON per.idInfluencer = p.owner_id
                        WHERE 
                          pc.aprovado = "1"
                        GROUP BY 
                          p.owner_id , per.profile_pic_url , per.username , per.full_name
                        ORDER BY pontos DESC
                          LIMIT 5';

        $resultTop5Ranking = DB::select(DB::raw($sqlTop5Ranking));
        $universe['top5Ranking'] = json_decode(json_encode($resultTop5Ranking), true);
        $universe['graphics']['feed'] = $this->_graphicFeedService->index();
        $universe['graphics']['weeklyImpact'] = $this->_graphicWeeklyImpactService->index();

        return $universe;
    }
}