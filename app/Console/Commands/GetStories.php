<?php
/**
 *
 * User: weslley ribeiro
 * Date: 24/08/2018
 * Time: 17:20
 * Description: Get stories of influencers on instagram and save all on database
 */

namespace Louder\Console\Commands;

use Illuminate\Console\Command;

class GetStories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Instagram:getStories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get stories of influencers on instagram and save all on database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
