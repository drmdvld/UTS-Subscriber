<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ConsumeUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $callback = function ($msg) {
            $data = json_decode($msg->body, true);
            User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
        };
        $channel->queue_declare('user_queue', false, false, false, false);
        $channel->basic_consume('user_queue', '', false, true, false, false, $callback);
        echo 'Waiting for new message on user_queue', " \n";
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}
