<?php

namespace Louder\Console\Commands\V1;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MigrateStoriesToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Instagram:V1.migrateStoriesToS3 {myConnection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate stories of influencers on server to S3 and replace all urlimg on database (this job belongs to louder 1.0)';
    protected $_carbon;
    protected $mydatabase;
    protected $_progressBar;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Carbon $carbon)
    {
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
        try{
            //TODO pegar videos do server mylouder
            $start  = 'Cron '.$this->signature.' Iniciada. '.$this->_carbon->format('d/m/Y H:i:s');
            Log::info($this->signature, ['Inicio' => $start]);
            $this->info($start."\n");
            $stories = DB::connection($this->argument('myConnection'))->select("SELECT
                                            *
                                          FROM
                                            Historias
                                          WHERE
                                            instagram_story_id IS NULL
                                          AND 
                                            midia_type IS NULL");
            //barra de progresso
            $this->_progressBar = $this->output->createProgressBar(count($stories));
            $this->_progressBar->setFormat('verbose');
            $this->_progressBar->setMaxSteps(count($stories));
            $this->_progressBar->setEmptyBarCharacter(' ');
            foreach ($stories as $story) {
                $this->info("\niniciando processo para o story: " . $story->urlimg);
                $urlFinal = trim("https://s3.us-east-2.amazonaws.com/mylouder/1/spoktoberfest/stories/2018/{$story->urlimg}");

                $resultsUpdateUrl = DB::connection($this->argument('myConnection'))->update("UPDATE 
                                                            Historias                                               
                                                          SET
                                                            temhashtag = 1,
                                                            urlimg     = '{$urlFinal}'
                                                          WHERE 
                                                            id = '{$story->id}' 
                                                            ");
                $this->info("\nNova url do story: {$urlFinal}");
                //avançando barra de status
                $this->_progressBar->advance();
            }
            //finalizando process bar
            $this->_progressBar->finish();
        }catch (Exception $ex){
            $this->error($ex->getMessage());
        }finally{
            //sempre executará
            $this->alert("\n\nFim do processo :)\n\n");
        }
    }
}