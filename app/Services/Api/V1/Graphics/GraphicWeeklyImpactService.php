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

    public function index($qtd = 7){

        $data = date('Y-m-d', strtotime("-" . ($qtd - 1) . " day"));
        $dataAtual = date('Y-m-d');
        
        $days = collect($this->_analyticsModel->select('data', 'postsHashtag')
                                              ->where('data', '>=', $data)
                                              ->where('postsHashtag', '>', '0')->get()->toArray());
        
        while($data <= $dataAtual){
            $dados[$data]['value'] = 0;
            $dados[$data]['label'] = ($data == $dataAtual) ? 'Hoje': getDiaSemana($data);

            $data = date('Y-m-d', strtotime("+1 day", strtotime($data)));
        }

        foreach($days as $day){
            $dados[$day['data']]['value'] += $day['postsHashtag'];
        }

        $return['labels'] = array_column($dados, 'label');
        $return['datasets']['impact']['label'] = 'Impacto (Ultimos ' . $qtd . ' dias)';
        $return['datasets']['data']  = array_column($dados, 'value');
        
        return $return;
    }
}