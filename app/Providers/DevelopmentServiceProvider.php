<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DevelopmentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * set the console as logger platform
         */
        \Event::listen('illuminate.log', function ($level, $message) {
            file_put_contents('php://stderr', 'Log:: '.$level." -> ". $message. PHP_EOL);
            file_put_contents('php://stderr', PHP_EOL);
        });
        /**
         * code to add database query in the console
         */
        if (isset($_SERVER['REQUEST_METHOD'])) {
            \Log::info("Request starts: ". $_SERVER['REQUEST_METHOD']." ". $_SERVER['REQUEST_URI']);
        }
        // log queries in console to debug
        \DB::listen(function ($queryExecuted) {
            $bindings = $queryExecuted->bindings;
            foreach ($bindings as $i => $binding) {
                if ($binding instanceof \DateTime) {
                    $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                } elseif (is_string($binding)) {
                    $bindings[$i] = "'$binding'";
                }
            }
            $query = str_replace(array('%', '?'), array('%%', '%s'), $queryExecuted->sql);
            $query = vsprintf($query, $bindings);
            \Log::info("[" . $queryExecuted->time . " ms] " . $query);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
