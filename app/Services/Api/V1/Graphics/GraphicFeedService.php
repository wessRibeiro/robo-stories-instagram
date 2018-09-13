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

class GraphicFeedService
{
    protected $_influencerModel;
    protected $_analyticsModel;
    protected $_router;
    protected $_request;

    public function __construct(Router          $router,
                                Influencer      $influencerModel,
                                Analytics       $analyticsModel,
                                Request         $request)
    {
        $this->_influencerModel  = $influencerModel;
        $this->_analyticsModel   = $analyticsModel;
        $this->_router           = $router;
        $this->_request          = $request;
    }

    public function index(){

        $return['labels'] = meses();
        $return['datasets']['posts']['label'] = 'Posts';
        //dd(collect($this->_analyticsModel->orderBy('dataHora')->get()));
        $return['datasets']['posts']['data']  = [1,4,34,5,5,6,7,45,93,310,511,12];

        $return['datasets']['comments']['label'] = 'Comentários';
        $return['datasets']['comments']['data']  = [1,2,3,4,55,6,57,8,59,130,131,142];

        $return['datasets']['likes']['label'] = 'Likes';
        $return['datasets']['likes']['data']  =   [1,2,3,4,35,36,37,8,59,10,511,512];

        return $return;
    }
}