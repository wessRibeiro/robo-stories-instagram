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

    protected $endPointApi = 'api.storiesig.com/stories/';
    protected $_guzzle;
    protected $_carbon;
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
        //barra de progresso
        $progressBar = $this->output->createProgressBar(count($influencers));
        $progressBar->setFormat('verbose');
        $progressBar->setMaxSteps(count($influencers));
        $progressBar->setEmptyBarCharacter(' ');
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

            foreach ($influencers as $influencer){
                //avançando a process bar
                $this->info("\niniciando processo para o influenciador:\nNome: ".$influencer->nome);
                //consumindo api
                $this->info("Url: {$this->endPointApi}{$influencer->instagram}");
                $response = $this->_guzzle->get($this->endPointApi.$influencer->instagram);
                dd($response);
                $progressBar->advance();
                $this->info("\n");
            }
        }catch (Exception $ex){
            $this->error($ex->getMessage());
            exit();
        }finally{
            //finalizando process bar
            $progressBar->finish();
        }

    }
}



