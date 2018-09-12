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

class GraphicService
{
    protected $_influencerModel;
    protected $_analyticsModel;
    protected $_storyModel;
    protected $_router;
    protected $_request;

    public function __construct(Router          $router,
                                Influencer      $influencerModel,
                                Story           $storyModel,
                                Analytics       $analyticsModel,
                                Request         $request)
    {
        $this->_influencerModel     = $influencerModel;
        $this->_storyModel          = $storyModel;
        $this->_analyticsModel      = $analyticsModel;
        $this->_router              = $router;
        $this->_request             = $request;
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

        $universe['postsPercent']    = number_format(($universe['sumPostsHashtag']*100)/$universe['sumPostsToday'], 1)."%";
        $universe['CommentsPercent'] = number_format(($universe['sumCommentsHashtag']*100)/$universe['sumCommentsToday'], 1)."%";
        $universe["likesPercent"]    = number_format(($universe['sumLikesHashtag']*100)/$universe['sumLikesToday'], 1)."%";
        $universe["universePercent"] = number_format(($universe['sumPostsHashtag']*100)/$universe["universeHashtag"], 1)."%";
        $universe['totalFollowers']  = collect();

        foreach ($this->_influencerModel->all() as $influencer){
            $universe['totalFollowers']->push($influencer->profile->followed_by);
        }
        $universe['totalFollowers']  = $universe['totalFollowers']->sum();
        $universe['engagement']      = round(($universe['sumLikesHashtag'] + $universe['sumCommentsHashtag'])/$universe['sumPostsHashtag'], 2);
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

        return $universe;
    }
}