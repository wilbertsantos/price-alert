<?php

namespace App\Services;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Intents;
use Illuminate\Support\Facades\Log;

class DiscordBot
{
    protected $discord;

    /**
     * DiscordBot constructor.
     */
    public function __construct()
    {
        $this->discord = new Discord([
            'token' => config('services.discord.bot_token'),
            'intents' => Intents::getDefaultIntents() | Intents::GUILD_MESSAGES,
        ]);

        $this->discord->on('ready', function (Discord $discord) {
            Log::info('Discord bot is ready.');
        });

        $this->discord->on('message', function (Message $message) {
            // Check if message is sent by bot
            if ($message->author->bot) {
                return;
            }

            // Check if message is sent in DM channel
            if (!$message->channel->isPrivate()) {
                return;
            }

            // Check if message content is in correct format
            $args = explode(' ', $message->content);

            if (count($args) !== 4) {
                $message->author->sendMessage('Invalid format. Format should be "!alert <coin> <price> <condition>".');
                return;
            }

            // Check if coin exists
            $coin = strtoupper($args[1]);
            if (!in_array($coin, config('services.binance.coins'))) {
                $message->author->sendMessage('Invalid coin. Available coins are: ' . implode(', ', config('services.binance.coins')) . '.');
                return;
            }

            // Check if price is numeric
            if (!is_numeric($args[2])) {
                $message->author->sendMessage('Invalid price. Price should be numeric.');
                return;
            }

            // Check if condition is valid
            $condition = strtolower($args[3]);
            if (!in_array($condition, ['lower', 'higher'])) {
                $message->author->sendMessage('Invalid condition. Condition should be "lower" or "higher".');
                return;
            }

            // Save alert to database
            $alert = auth()->user()->alerts()->create([
                'coin' => $coin,
                'price' => $args[2],
                'condition' => $condition,
                'status' => 'active',
            ]);

            $message->author->sendMessage('Alert created successfully.');

            Log::info('Alert created by Discord bot', [
                'user_id' => auth()->user()->id,
                'coin' => $coin,
                'price' => $args[2],
                'condition' => $condition,
                'alert_id' => $alert->id,
            ]);
        });
    }

    /**
     * Start the Discord bot.
     *
     * @return void
     */
    public function start()
    {
        $this->discord->run();
    }
}
