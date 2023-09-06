<?php

namespace App\Console\Commands;

use App\Events\EventsBotNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BotNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $quotes = [
            'Detect typing and Seen message are available in Private Chat. Select an user from The right Sidebar and start Private Chat',
            'You can react to other user\'s message (Love, Haha, Angry,...)'
        ];
        
  
        $randIndex = array_rand($quotes);
        $message = $quotes[$randIndex];
        // dd($message);
        broadcast(new EventsBotNotification($message));
        Log::info('Bot notification sent');
    }
}
