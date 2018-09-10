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
        $return = [
                    'totalActiveInfluencers'    => $this->_influencerModel->where('ativo', true)->count(),
                    'totalStoriesHashtag'       => $this->_storyModel->where('temhashtag', true)->count(),
                    'totalFollowers'            => '',
                    'sumLikesToday'             => collect($this->_analyticsModel->pluck('likesHoje'))->sum(),
                    'sumPostsToday'             => collect($this->_analyticsModel->pluck('postsHoje'))->sum(),
                    'sumCommentsToday'          => collect($this->_analyticsModel->pluck('comentariosHoje'))->sum(),
                    'sumLikesHashtag'           => collect($this->_analyticsModel->pluck('likesHashtag'))->sum(),
                    'sumPostsHashtag'           => collect($this->_analyticsModel->pluck('postsHashtag'))->sum(),
                    'sumCommentsHashtag'        => collect($this->_analyticsModel->pluck('comentariosHashtag'))->sum(),
                    'universoHashtag'           => collect($this->_analyticsModel->pluck('universoHashtag'))->pop(),

                  ];

        return $return;
    }
}