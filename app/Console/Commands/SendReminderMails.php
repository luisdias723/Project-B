<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\EventController;

use Carbon\Carbon;
use DateTime;
use DateInterval;

class SendReminderMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SendReminderMails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a reminder when an event is within 24 hours';

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
        $events = Event::all();

        foreach($events as $event){

            $data_users = DB::table('events')
            ->where('events.id', '=', $event['id'])
            ->leftJoin('users as client', function($join){
                $join->on('events.client_id', '=' , 'client.id');
            })
            ->leftJoin('users as doctor', function($join){
                $join->on('events.doctor_id', '=' , 'doctor.id');
            })
            ->get([
                'client.id as client_id',
                'client.email as client_email',
                'doctor.id as doctor_id',
                'doctor.email as doctor_email',
                'doctor.name as doctor_name',
            ]);

            date_default_timezone_set('Europe/Lisbon');

            $date_start = DB::table('events')->where('id' , '=', $event['id'])->pluck('date_start');
            $date_start = $date_start[0];

            $currentDate = date("Y-m-d H:i:s");
            $date_start_condition = date('Y-m-d H:i:s', strtotime($date_start . ' -1 day'));
            

            if($date_start > $date_start_condition &&  $date_start < $currentDate){
                $var = new EventController;
                $var->sendReminderMail($event, [$data_users[0]->client_email, $data_users[0]->doctor_email]);
                echo "true";
            }
            else{
                echo "false";
            }
        }
    }
}
