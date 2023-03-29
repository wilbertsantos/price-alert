<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Discord\Discord;
use Discord\Parts\User\Activity;

class DiscordServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Discord::class, function ($app) {
            $discord = new Discord([
                'token' => env('DISCORD_BOT_TOKEN'),
                'logger' => $app['log']->getLogger(),
            ]);

            $discord->on('ready', function (Discord $discord) {
                $activity = new Activity([
                    'name' => 'your bot status message',
                    'type' => Activity::TYPE_WATCHING,
                ]);

                $discord->updatePresence($activity);
            });

            return $discord;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
