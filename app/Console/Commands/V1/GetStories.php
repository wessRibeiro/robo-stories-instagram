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
    protected $_guzzle;
    protected $_carbon;
    protected $columns;
    protected $progressBar;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Client $guzzle, Carbon $carbon)
    {
        $this->_guzzle	= $guzzle;
        $this->_carbon	= $carbon;

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
            $influencers = DB::select(' SELECT 
                                                * 
                                              FROM 
                                                Influencers 
                                              WHERE
                                                ativo = 1
                                             ');
            //barra de progresso
            $progressBar = $this->output->createProgressBar(count($influencers));
            $progressBar->setFormat('verbose');
            $progressBar->setMaxSteps(count($influencers));
            $progressBar->setEmptyBarCharacter(' ');
            foreach ($influencers as $influencer){
                $salvarAlteracao = false;
                //avançando a process bar
                $this->info("\niniciando processo para o influenciador:\nNome: ".$influencer->nome);
                //consumindo api
                $this->info("Url: {$this->endPointApi}{$influencer->instagram}");
                $response = $this->_guzzle->get($this->endPointApi.$influencer->instagram);
                $response = json_decode($response->getBody() , true );
                //verificando se é user privado
                if(!$response['user']['is_private']){
                    $this->info('Usuario com visibilidade publica.');
                    //verificando se alguma informação esta desatualizada
                    #nome
                    if (strrpos($influencer->nome, $response['user']['full_name']) === false) {
                        $this->error('> Nome diferente do Instagram, estamos atualizando.');
                        $results = DB::update("UPDATE 
                                                        Influencers                                               
                                                      SET
                                                        nome      = '{$response['user']['full_name']}'
                                                      WHERE 
                                                        instagram = '{$influencer->instagram}' 
                                                        ");
                    }
                    #img perfil
                    if (strrpos($influencer->img, $response['user']['profile_pic_url']) === false) {
                        $this->error('> Imagem de perfil diferente do Instagram, estamos atualizando.');
                        $influencer->img  = $response['user']['profile_pic_url'];
                        $results = DB::update("UPDATE 
                                                        Influencers 
                                                      SET
                                                        img       = '{$response['user']['profile_pic_url']}'
                                                      WHERE 
                                                        instagram = '{$influencer->instagram}'                                                  
                                                      ");
                    }
                    $progressBar->advance();
                    $this->info("\n\n");
                }else{
                    $this->error('Usuario com visibilidade privada.');
                    continue;
                }
            }
        }catch (\GuzzleHttp\Exception\ClientException $ex){
            $responseBodyAsString = $ex->getResponse()->getBody()->getContents();
            $response = json_decode($responseBodyAsString);
            if( is_object($response)) {
                $response = (array)$response;
            }
            $this->error($response);
            $this->info("\nEsperando 1 min para requisitar novamente...");
        }catch (Exception $ex){

            $responseBodyAsString = $ex->getResponse()->getBody()->getContents();
            $response = json_decode($responseBodyAsString);
            if( is_object($response)) {
                $response = (array)$response;
            }
            $this->error($response);
            exit();

        }finally{
            //finalizando process bar
            $progressBar->finish();
        }

    }
}



