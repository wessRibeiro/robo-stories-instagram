<?php
/**
 *
 * User: weslley ribeiro
 * Date: 24/08/2018
 * Time: 17:20
 * Description: Get stories of influencers on instagram and save all on database (this job belongs to louder 1.0)
 */

namespace Louder\Console\Commands\V1;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GetStories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Instagram:V1.GetStories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get stories of influencers on instagram and save all on database (this job belongs to louder 1.0)';

    protected $endPointApi = 'http://api.storiesig.com/stories/';
    protected $pathS3;
    protected $_guzzle;
    protected $_carbon;
    protected $_progressBar;
    protected $temHashtagPrograma;
    protected $regexStories = '(.*.jpg|.png|.jpeg|.gif|.mp4)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Client $guzzle, Carbon $carbon)
    {
        $this->_guzzle	= $guzzle;
        $this->_carbon	= $carbon;
        $this->pathS3   = "Stories/{$this->_carbon->format('Y')}/";
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

            $start  = 'Cron '.$this->signature.' Iniciada. '.$this->_carbon->format('d/m/Y H:i:s');
            Log::info($this->signature, ['Inicio' => $start]);
            $this->info($start."\n");
            /*
             * @TODO relacionar foreach de programas com conexão
             * */
            $programs = DB::connection('louderhub')
                             ->select("SELECT
                                                *
                                              FROM
                                                programs
                                              WHERE
                                                name = 'missaoveja'");
            foreach ($programs as $program) {
                $influencers = DB::select(' SELECT 
                                                    * 
                                                  FROM 
                                                    Influencers 
                                                  WHERE
                                                    ativo = 1');
                //barra de progresso
                $this->_progressBar = $this->output->createProgressBar(count($influencers));
                $this->_progressBar->setFormat('verbose');
                $this->_progressBar->setMaxSteps(count($influencers));
                $this->_progressBar->setEmptyBarCharacter(' ');

                //influenciadores
                foreach ($influencers as $influencer) {
                    $this->info("\niniciando processo para o influenciador:\nNome: " . $influencer->nome);
                    //consumindo api
                    $this->info("Url: {$this->endPointApi}{$influencer->instagram}");
                    $responseStories = $this->_guzzle->get($this->endPointApi . $influencer->instagram);
                    $responseStories = json_decode($responseStories->getBody(), true);
                    //verificando se é user privado
                    if (!$responseStories['user']['is_private']) {
                        $this->info('Usuario com visibilidade publica.');
                        //verificando se alguma informação esta desatualizada
                        #nome
                        if (strrpos($influencer->nome, $responseStories['user']['full_name']) === false) {
                            $this->error('> Nome diferente do Instagram, estamos atualizando.');
                            $resultsUpdateName = DB::update("UPDATE 
                                                                    Influencers                                               
                                                                  SET
                                                                    nome      = '".trim($responseStories['user']['full_name'])."'
                                                                  WHERE 
                                                                    instagram = '{$influencer->instagram}' 
                                                                    ");
                        }
                        //tratando url
                        $urlProfilePic = pregString($this->regexStories, $responseStories['user']['profile_pic_url']);
                        #img perfil
                        if (strrpos($influencer->img, $urlProfilePic) === false) {
                            $this->error('> Imagem de perfil diferente do Instagram, estamos atualizando.');
                            $resultsUpdateInfluencer = DB::update("UPDATE 
                                                                            Influencers 
                                                                          SET
                                                                            img       = '{$urlProfilePic}'
                                                                          WHERE 
                                                                            instagram = '{$influencer->instagram}'                                                  
                                                                          ");
                        }

                        $this->info("Hashtags do programa: {$program->hashtags}\n");

                        //stories do influenciador
                        $this->info("Comecando processo de Stories do influencer...\n");
                        $this->info("\n------------------------------------------------\n");
                        foreach ($responseStories['items'] as $storie){
                            //verificando se o storie ja esta no banco
                            $resultsInfluencerHasStorie = DB::select("SELECT
                                                                                * 
                                                                             FROM 
                                                                                Historias 
                                                                             WHERE 
                                                                                idUser = :influencers_id
                                                                             AND 
                                                                                instagram_storie_id = :instagram_storie_id",
                                                                      [
                                                                          'influencers_id'      => $influencer->id,
                                                                          'instagram_storie_id' => $storie['pk'],
                                                                      ]);
                            if(!$resultsInfluencerHasStorie){
                                $this->info("> Salvando Storie de id:{$storie['pk']}.");
                                //verificando se o storie tem hashtag
                                if($storie['story_hashtags']) {
                                    foreach ($storie['story_hashtags'] as $hashtags){
                                        //se hash storie está nas hashs do programa
                                        if(in_array($hashtags['hashtag']['name'], explode(',', $program->hashtags))){
                                            $this->temHashtagPrograma = true;
                                            break;
                                        }
                                    }
                                }

                                //verificando se é imagem ou video
                                if($storie['media_type'] == 1){ #imagem
                                    if($this->temHashtagPrograma){
                                        $explodeUrl = explode('/', $storie['image_versions2']['candidates'][2]['url']);
                                        $pathStories = $this->pathS3.pregString($this->regexStories, end($explodeUrl));

                                        if(!Storage::disk('s3')->exists($pathStories)){

                                            Storage::disk('s3')->put($pathStories,
                                                file_get_contents($storie['image_versions2']['candidates'][2]['url'])
                                            );

                                            $resultsInsertStorie = DB::table('Historias')
                                                ->insert(
                                                    [
                                                        'aplicativo'            => 1,
                                                        'email'                 => $influencer->email,
                                                        'temhashtag'            => 1,
                                                        'temcitacao'            => NULL,
                                                        'descricao'             => NULL,
                                                        'aprovado'              => 0,
                                                        'justificativa'         => 0,
                                                        'vinculadoem'           => date('Y-m-d h:m:s', $storie['taken_at']),
                                                        'urlimg'                => Storage::disk('s3')->url($pathStories),
                                                        'pontos'                => 0,
                                                        'idUser'                => $influencer->id,
                                                        'midia_type'            => $storie['media_type'],
                                                        'instagram_storie_id'   => $storie['pk'],
                                                    ]
                                                );
                                        }
                                    }else{
                                        $resultsInsertStorie = DB::table('Historias')
                                            ->insert(
                                                [
                                                    'aplicativo'            => 1,
                                                    'email'                 => $influencer->email,
                                                    'temhashtag'            => 0,
                                                    'temcitacao'            => NULL,
                                                    'descricao'             => NULL,
                                                    'aprovado'              => 0,
                                                    'justificativa'         => 0,
                                                    'vinculadoem'           => date('Y-m-d h:m:s', $storie['taken_at']),
                                                    'urlimg'                => pregString($this->regexStories, $storie['image_versions2']['candidates'][2]['url']),
                                                    'pontos'                => 0,
                                                    'idUser'                => $influencer->id,
                                                    'midia_type'            => $storie['media_type'],
                                                    'instagram_storie_id'   => $storie['pk'],
                                                ]
                                            );
                                    }
                                }elseif ($storie['media_type'] == 2){#video
                                    if($this->temHashtagPrograma){
                                        $explodeUrl = explode('/', end($storie['video_versions'])['url']);
                                        $pathStories = $this->pathS3.end($explodeUrl);

                                        if(!Storage::disk('s3')->exists($pathStories)){

                                            Storage::disk('s3')->put($pathStories,
                                                                            file_get_contents(end($storie['video_versions'])['url'])
                                                                           );

                                            $resultsInsertStorie = DB::table('Historias')
                                                ->insert(
                                                    [
                                                        'aplicativo'            => 1,
                                                        'email'                 => $influencer->email,
                                                        'temhashtag'            => 1,
                                                        'temcitacao'            => NULL,
                                                        'descricao'             => NULL,
                                                        'aprovado'              => 0,
                                                        'justificativa'         => 0,
                                                        'vinculadoem'           => date('Y-m-d h:m:s', $storie['taken_at']),
                                                        'urlimg'                => Storage::disk('s3')->url($pathStories),
                                                        'pontos'                => 0,
                                                        'idUser'                => $influencer->id,
                                                        'midia_type'            => $storie['media_type'],
                                                        'instagram_storie_id'   => $storie['pk'],
                                                    ]
                                                );
                                        }
                                    }else{
                                        $resultsInsertStorie = DB::table('Historias')
                                            ->insert(
                                                [
                                                    'aplicativo'            => 1,
                                                    'email'                 => $influencer->email,
                                                    'temhashtag'            => 0,
                                                    'temcitacao'            => NULL,
                                                    'descricao'             => NULL,
                                                    'aprovado'              => 0,
                                                    'justificativa'         => 0,
                                                    'vinculadoem'           => date('Y-m-d h:m:s', $storie['taken_at']),
                                                    'urlimg'                => end($storie['video_versions'])['url'],
                                                    'pontos'                => 0,
                                                    'idUser'                => $influencer->id,
                                                    'midia_type'            => $storie['media_type'],
                                                    'instagram_storie_id'   => $storie['pk'],
                                                ]
                                            );

                                    }
                                }
                            }else{
                                $this->info("> Storie de id:{$storie['pk']} já está na base.");
                                continue;
                            }
                            $this->temHashtagPrograma = false;
                        }
                        $this->info("\n------------------------------------------------\n");
                        //avançando barra de status
                        $this->_progressBar->advance();

                    } else {
                        $this->error('Usuario com visibilidade privada.');
                        continue;
                    }

                }//foreach influencers

                //finalizando process bar
                $this->_progressBar->finish();
            }//foreach programs
        }catch (\GuzzleHttp\Exception\ClientException $ex){
            $responseStoriesBodyAsString = $ex->getResponse()->getBody()->getContents();
            $responseStories = json_decode($responseStoriesBodyAsString);
            if( is_object($responseStories)) {
                $responseStories = (array)$responseStories;
            }
            $this->error($responseStories);
            $this->alert("\nEsperando 1 min para requisitar novamente...");

        }catch (\Illuminate\Database\QueryException $ex){
            $this->error($ex->getMessage());

        }catch (Exception $ex){
            $this->error($ex->getMessage());

        }finally{
            //sempre executara
            $this->alert("Fim do processo :)\n");
        }

    }
}



