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

class GraphicWeeklyImpactService
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
        $return['labels'] = semanas();
        $return['datasets']['impact']['label'] = 'Impacto';
        $return['datasets']['data']  = [1,2,3,4,5,6,7];
        return $return;
    }
}