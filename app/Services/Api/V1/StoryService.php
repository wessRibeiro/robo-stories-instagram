<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05/09/2018
 * Time: 13:53.
 */

namespace Louder\Services\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Louder\Models\V1\Influencer;
use Louder\Models\V1\Story;
use Louder\Models\V1\Missoes;

class StoryService
{
    protected $_influencerModel;
    protected $_storyModel;
    protected $_router;
    protected $_request;

    public function __construct(Route           $route,
                                Influencer      $influencerModel,
                                Story           $storyModel,
                                Missoes         $missoesModel,
                                Request         $request)
    {
        $this->_route           = $route;
        $this->_request         = $request;
        $this->_influencerModel = $influencerModel->setConnection($this->_route->parameter('program'));
        $this->_storyModel      = $storyModel->setConnection($this->_route->parameter('program'));
        $this->_missoesModel    = $missoesModel->setConnection($this->_route->parameter('program'));
    }

    public function index()
    {
        $dataInfluencerHasStories = [];

        $stories = $this->_storyModel->join('Influencers', 'Influencers.id', '=', 'Historias.iduser')
                                     ->where('Historias.temhashtag', '=', true)
                                     ->where('Historias.aprovado', '=', false)
                                     ->where('Influencers.ativo', '=', true)
                                     ->limit(50)
                                     ->get();
                                     
        foreach($stories as $story){
            array_push($dataInfluencerHasStories,
                [
                    'name'              => trim($story->nome),
                    'instagramUser'     => $story->instagram,
                    'pictureProfile'    => $story->img,
                    'urlStory'          => $story->urlimg,
                    'datePost'          => mysql_br_date_time($story->vinculadoem),
                    'midiaType'         => $story->midia_type,
                    'instagramStoryId'  => $story->instagram_story_id,
                    'is_geral'          => $story->is_geral,
                ]
            );
        }

        return $dataInfluencerHasStories;
    }

    public function getTotalStories()
    {
        $total = $this->_storyModel->join('Influencers', 'Influencers.id', '=', 'Historias.iduser')
                                     ->where('Historias.temhashtag', '=', true)
                                     ->where('Historias.aprovado', '=', false)
                                     ->where('Influencers.ativo', '=', true)
                                     ->count();

        return $total;
    }

    public function getInfluencers()
    {
        return $this->_influencerModel->where('ativo', '=', 1)->get();
    }

    public function approve($instagramStoryId, $missao)
    {
        try {
            if(!empty($missao)){
                $pontos = current($this->_missoesModel->select('pontos_instagram')->where('id', '=', $missao)->get()->toArray())['pontos_instagram'];
            } else {
                $pontos = 0;
            }

            $update                  = array();
            $update['aprovado']      = 1;
            $update['justificativa'] = null;
            $update['pontos']        = $pontos;

            
            if ($this->_request->get('influencerId') != 0) {
                $update['iduser'] = $this->_request->get('influencerId');
            }

            if ($this->_storyModel->where('instagram_story_id', '=', $instagramStoryId)->update($update)) {
                return [
                    'code'    => 200,
                    'message' => 'Story aprovado!',
                ];
            } else {
                return [
                    'code'    => 404,
                    'message' => 'Erro ao tentar aprovar!',
                ];
            }
        } catch (\Exception $ex) {
            return [
                'code'    => 400,
                'message' => $ex->getMessage(),
            ];
        }
    }

    public function disapprove($instagramStoryId)
    {
        try {
            if ($this->_storyModel->where('instagram_story_id', '=', $instagramStoryId)
                                  ->update([
                                        'aprovado' => 2,
                                        'justificativa' => $this->_request->get('justification'),
                                    ])
            ) {
                return [
                    'code'    => 200,
                    'message' => 'Story reprovado!',
                ];
            } else {
                return [
                    'code'    => 404,
                    'message' => 'Erro ao tentar reprovar!',
                ];
            }
        } catch (\Exception $ex) {
            return [
                'code'    => 400,
                'message' => $ex->getMessage(),
            ];
        }
    }
}
