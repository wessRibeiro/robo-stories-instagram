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

class GetStoriesLouder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Instagram:V1.GetStoriesLouder';

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
    protected $start;
    protected $temHashtagPrograma;
    protected $oneMoreLastTime = false;
    protected $regexStories = '/([^*]*)(.*.jpg|.png|.jpeg|.gif|.mp4)/';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Client $guzzle, Carbon $carbon)
    {
        $this->_guzzle	= $guzzle;
        $this->_carbon	= $carbon;
        $this->pathS3  = "";
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
            startGoto:
                $startProcess  = 'Cron '.$this->signature.' Iniciada. '.$this->_carbon->format('d/m/Y H:i:s');
                Log::info($this->signature, ['Inicio' => $startProcess]);
                $this->info($startProcess."\n");
                /*
                 * @TODO relacionar foreach de programas com conexão
                 * */
                $programs = DB::select("SELECT
                                *
                              FROM
                                programs
                             ");

                foreach ($programs as $program) {

                    //obtendo configurações do programa
                    $configurations = json_decode($program->configurations);

                    //verificando se deve rodar robo do louderbase
                    if(!isset($configurations->curatorship->robo_stories_enable) ||
                       $configurations->curatorship->robo_stories_enable) {

                        //configurando pasta S3 para o programa tratado
                        $this->pathS3 = "{$program->agency_id}/{$program->name}/stories/{$this->_carbon->format('Y')}/";

                        $influencers = DB::connection($program->name)->select(' SELECT 
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

                        $this->info("\n");
                        $this->alert("> iniciando processo para o programa: '{$program->name}', aguarde 8 min");
                        //se vai baixar todos os posts sem considerar hashtags
                        if(!$configurations->curatorship->only_with_hashtag){
                            $this->alert("> programa: '{$program->name}', baixa sem hashtag");
                        }
                        //aguardando 8 min para executar o consumo
                        //sleep(480);
                        $cont = 0;
                        //influenciadores
                        foreach ($influencers as $influencer) {
                            $this->info("\niniciando processo para o influenciador:\nNome: {$influencer->nome} Hora: {$this->_carbon->format('d/m/Y H:i:s')}");
                            //consumindo api
                            $this->info("Url: {$this->endPointApi}{$influencer->instagram}");
                            $responseStories = $this->_guzzle->get(trim($this->endPointApi . $influencer->instagram));
                            $responseStories = json_decode($responseStories->getBody(), true);
                            //verificando se é user privado
                            if (!$responseStories['user']['is_private']) {
                                $this->info('Usuario com visibilidade publica.');
                                //verificando se alguma informação esta desatualizada
                                #nome
                                if (strrpos($influencer->nome, $responseStories['user']['full_name']) === false) {
                                    $this->error('> Nome diferente do Instagram, estamos atualizando.');
                                    $resultsUpdateName = DB::connection($program->name)->update("UPDATE 
                                                                            Influencers                                               
                                                                          SET
                                                                            nome      = '" . trim($responseStories['user']['full_name']) . "'
                                                                          WHERE 
                                                                            instagram = '{$influencer->instagram}' 
                                                                            ");
                                }
                                /*
                                //tratando url
                                $urlProfilePic = pregString($this->regexStories, $responseStories['user']['profile_pic_url']);
                                #img perfil
                                if (strrpos($influencer->img, $urlProfilePic) === false) {
                                    $this->error('> Imagem de perfil diferente do Instagram, estamos atualizando.');
                                    $resultsUpdateInfluencer = DB::connection($program->name)->update("UPDATE 
                                                                                    Influencers 
                                                                                  SET
                                                                                    img       = '{$urlProfilePic}'
                                                                                  WHERE 
                                                                                    instagram = '{$influencer->instagram}'                                                  
                                                                                  ");
                                }
                                */

                                $this->info("Hashtags do programa: {$program->hashtags}\n");

                                //stories do influenciador
                                $this->info("Comecando processo de Stories do influencer...\n");
                                $this->info("\n------------------------------------------------\n");
                                foreach ($responseStories['items'] as $story) {
                                    //verificando se o story ja esta no banco
                                    $resultsInfluencerHasStory = DB::connection($program->name)->select("SELECT
                                                                                * 
                                                                             FROM 
                                                                                Historias 
                                                                             WHERE 
                                                                                instagram_story_id = :instagram_story_id",
                                        [
                                            'instagram_story_id' => $story['pk'],
                                        ]);
                                    if (!$resultsInfluencerHasStory) {
                                        $this->info("> Salvando Story de id:{$story['pk']}.");
                                        //verificando se o Story tem hashtag
                                        if (isset($story['story_hashtags']) ||
                                            $influencer->is_geral           ||
                                            !$configurations->curatorship->only_with_hashtag
                                            ){
                                            if ($influencer->is_geral || !$configurations->curatorship->only_with_hashtag) {
                                                $this->temHashtagPrograma = true;
                                            } else {
                                                foreach ($story['story_hashtags'] as $hashtags) {
                                                    //se hash Story está nas hashs do programa
                                                    if (in_array($hashtags['hashtag']['name'], explode(', ', $program->hashtags)) || $influencer->is_geral) {
                                                        $this->temHashtagPrograma = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        }

                                        //verificando se é imagem ou video
                                        if ($story['media_type'] == 1) { #imagem
                                            if ($this->temHashtagPrograma) {
                                                $explodeUrl = explode('/', $story['image_versions2']['candidates'][2]['url']);
                                                $pathStories = $this->pathS3 . pregString($this->regexStories, end($explodeUrl));

                                                if (!Storage::disk('s3')->exists($pathStories)) {
                                                    //subindo no s3
                                                    Storage::disk('s3')->put($pathStories,
                                                        file_get_contents($story['image_versions2']['candidates'][2]['url'])
                                                    );

                                                }
                                                $resultsInsertStory = DB::connection($program->name)->table('Historias')
                                                    ->insert(
                                                        [
                                                            'aplicativo'            => 1,
                                                            'email'                 => $influencer->email,
                                                            'temhashtag'            => 1,
                                                            'temcitacao'            => NULL,
                                                            'descricao'             => NULL,
                                                            'aprovado'              => 0,
                                                            'justificativa'         => 0,
                                                            'vinculadoem'           => date('Y-m-d H:m:s', $story['taken_at']),
                                                            'urlimg'                => Storage::disk('s3')->url($pathStories),
                                                            'pontos'                => 0,
                                                            'idUser'                => $influencer->id,
                                                            'midia_type'            => $story['media_type'],
                                                            'instagram_story_id'    => $story['pk'],
                                                        ]
                                                    );
                                            } else {
                                                $resultsInsertStory = DB::connection($program->name)->table('Historias')
                                                    ->insert(
                                                        [
                                                            'aplicativo'            => 1,
                                                            'email'                 => $influencer->email,
                                                            'temhashtag'            => 0,
                                                            'temcitacao'            => NULL,
                                                            'descricao'             => NULL,
                                                            'aprovado'              => 0,
                                                            'justificativa'         => 0,
                                                            'vinculadoem'           => date('Y-m-d H:m:s', $story['taken_at']),
                                                            'urlimg'                => pregString($this->regexStories, $story['image_versions2']['candidates'][2]['url']),
                                                            'pontos'                => 0,
                                                            'idUser'                => $influencer->id,
                                                            'midia_type'            => $story['media_type'],
                                                            'instagram_story_id'    => $story['pk'],
                                                        ]
                                                    );
                                            }
                                        } elseif ($story['media_type'] == 2) {#video
                                            if ($this->temHashtagPrograma) {
                                                $explodeUrl = explode('/', end($story['video_versions'])['url']);
                                                $pathStories = pregString($this->regexStories, $this->pathS3 . end($explodeUrl));
                                                //video
                                                if (!Storage::disk('s3')->exists($pathStories)) {

                                                    Storage::disk('s3')->put($pathStories,
                                                        file_get_contents(end($story['video_versions'])['url'])
                                                    );

                                                }
                                                //frame do video
                                                $explodeUrlFrame = explode('/', $story['image_versions2']['candidates'][2]['url']);
                                                $pathStoriesFrame = "{$this->pathS3}frames_videos/" . pregString($this->regexStories, end($explodeUrlFrame));

                                                if (!Storage::disk('s3')->exists($pathStoriesFrame)) {

                                                    Storage::disk('s3')->put($pathStoriesFrame,
                                                        file_get_contents($story['image_versions2']['candidates'][2]['url'])
                                                    );

                                                }

                                                $resultsInsertStory = DB::connection($program->name)->table('Historias')
                                                    ->insert(
                                                        [
                                                            'aplicativo'            => 1,
                                                            'email'                 => $influencer->email,
                                                            'temhashtag'            => 1,
                                                            'temcitacao'            => NULL,
                                                            'descricao'             => NULL,
                                                            'aprovado'              => 0,
                                                            'justificativa'         => 0,
                                                            'vinculadoem'           => date('Y-m-d H:m:s', $story['taken_at']),
                                                            'urlimg'                => Storage::disk('s3')->url($pathStories),
                                                            'pontos'                => 0,
                                                            'idUser'                => $influencer->id,
                                                            'midia_type'            => $story['media_type'],
                                                            'instagram_story_id'    => $story['pk'],
                                                            'image_video_url'       => Storage::disk('s3')->url($pathStoriesFrame),
                                                        ]
                                                    );
                                            } else {
                                                $resultsInsertStory = DB::connection($program->name)->table('Historias')
                                                    ->insert(
                                                        [
                                                            'aplicativo'            => 1,
                                                            'email'                 => $influencer->email,
                                                            'temhashtag'            => 0,
                                                            'temcitacao'            => NULL,
                                                            'descricao'             => NULL,
                                                            'aprovado'              => 0,
                                                            'justificativa'         => 0,
                                                            'vinculadoem'           => date('Y-m-d H:m:s', $story['taken_at']),
                                                            'urlimg'                => end($story['video_versions'])['url'],
                                                            'pontos'                => 0,
                                                            'idUser'                => $influencer->id,
                                                            'midia_type'            => $story['media_type'],
                                                            'instagram_story_id'    => $story['pk'],
                                                        ]
                                                    );

                                            }
                                        }
                                    } else {
                                        $this->info("> Story de id:{$story['pk']} já está na base.");
                                        continue;
                                    }
                                    $this->temHashtagPrograma = false;
                                }
                                $this->info("\n------------------------------------------------\n");
                                //avançando barra de status
                                $this->_progressBar->advance();
                                $this->info("\n");

                            } else {
                                $this->error('Usuario com visibilidade privada.');
                                continue;
                            }
                            $cont++;
                            if ($cont >= 16) {
                                $this->alert("\nrobo correu 15 influenciadores Esperando 5 min para requisitar novamente...\n");
                                //esperando 5 min para consumir
                                //sleep(300);
                                $cont = 0;
                            }
                        }//foreach influencers

                        //finalizando process bar
                        $this->_progressBar->finish();
                    }else{
                        $this->info("> Programa: {$program->name} não é louderbase ou o robo está desativado.");
                        $this->info("\n------------------------------------------------\n");
                        continue;
                    }
                }//foreach programs
            //fim goto
        }catch (\GuzzleHttp\Exception\RequestException $ex){
            $this->error("\n-----------ERROR-------------\n".$ex->getMessage()."\n------------------------\n");
            if(!$this->oneMoreLastTime){
                $this->alert("Reiniciando...");
                $this->oneMoreLastTime = true;
                goto startGoto;
            }else{
                $this->alert("Fim do processo, depois de 2 tentativas :)");
                exit();
            }

        }catch (\Illuminate\Database\QueryException $ex){
            $this->error("\n-----------ERROR-------------\n".$ex->getMessage()."\n------------------------\n");

        }catch (\Exception $ex){
            $this->error("\n-----------ERROR-------------\n".$ex->getMessage()."\n------------------------\n");
            if(!$this->oneMoreLastTime){
                $this->alert("Reiniciando...");
                $this->oneMoreLastTime = true;
                goto startGoto;
            }else{
                $this->alert("Fim do processo, depois de 2 tentativas :)");
                exit();
            }
        }finally{
            //sempre executara
            $this->info("\n------------------------------------------------\n");
            $this->alert("Fim do processo :)");
        }

    }
}