<?php

namespace Louder\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * User: weslley ribeiro
         * Date: 12/11/2018
         * Time: 15:56
         * Description: insere no config.database, todas as conexões do louderHub
         */
        setConnectionsHub();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
