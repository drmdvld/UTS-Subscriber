<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ConsumeUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume:user-created';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASS'), env('MQ_VHOST'));
        $channel = $connection->channel();
        $createCallback = function ($msg) {
            $data = json_decode($msg->body, true);
            User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            echo ' [x] Success add Data ', $msg->body, "\n";
        };
        $updateCallback = function ($msg) {
            $data = json_decode($msg->body, true);
            $user = User::find($data['id']);

            $user->update([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
            ]);

            echo ' [x] Success update Data ', $msg->body, "\n";
        };

        $deleteCallback = function ($msg) {
            $data = json_decode($msg->body, true);

            $user = User::find($data['id']);
            $user->delete();

            echo ' [x] Success delete Data ', $msg->body, "\n";
        };
        $channel->queue_declare('user_create_queue', false, false, false, false);
        $channel->basic_consume('user_create_queue', '', false, true, false, false, $createCallback);

        $channel->queue_declare('user_update_queue', false, false, false, false);
        $channel->basic_consume('user_update_queue', '', false, true, false, false, $updateCallback);

        $channel->queue_declare('user_delete_queue', false, false, false, false);
        $channel->basic_consume('user_delete_queue', '', false, true, false, false, $deleteCallback);
        echo 'Waiting for new message', " \n";
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}
