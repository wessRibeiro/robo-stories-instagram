<?php

namespace Louder\Console\Commands\V1;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MigrateStoriesToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Instagram:V1.migrateStoriesToS3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate stories of influencers on server to S3 and replace all urlimg on database (this job belongs to louder 1.0)';
    protected $_carbon;
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
        $start  = 'Cron '.$this->signature.' Iniciada. '.$this->_carbon->format('d/m/Y H:i:s');
        Log::info($this->signature, ['Inicio' => $start]);
        $this->info($start."\n");
        $stories = DB::select("SELECT
                                        *
                                      FROM
                                        Historias
                                      WHERE
                                        instagram_storie_id = ''");
        //barra de progresso
        $this->_progressBar = $this->output->createProgressBar(count($stories));
        $this->_progressBar->setFormat('verbose');
        $this->_progressBar->setMaxSteps(count($stories));
        $this->_progressBar->setEmptyBarCharacter(' ');
        foreach ($stories as $story) {
            $this->info("\niniciando processo para o influenciador:\nNome: " . $influencer->nome);
        }
    }
}
