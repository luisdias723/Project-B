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

class sendReminderMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:mail_reminder';

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
    public function handle(Request $request)
    {

        $params = $request->all();
        //echo "<pre>", print_r($date_start);
        $events = Event::all();


        foreach($events as $event){

            $date_start = DB::table('events')->where('id' , '=', $events['id'])->pluck('date_start');

            $email = DB::table('events')
                            ->where('events.id', '=', $event)
                            ->leftJoin('users as client', function($join){
                                $join->on('events.client_id', '=' , 'client.id');
                            })
                            ->leftJoin('users as doctor', function($join){
                                $join->on('events.doctor_id', '=' , 'doctor.id');
                            })

                            ->first([
                                'client.id as client_id',
                                'client.email as client_email',
                                'doctor.id as doctor_id',
                                'doctor.email as doctor_email',
                                'doctor.name as doctor_name',
                            ]);

                            echo "<pre>", print_r($email);

            $event_date = [$date_start]; //get date_start of event
            //trying to send email for an event in 24h
            $current_time = Carbon::now('Europe/Lisbon');  //current time
            $condition_timestamp = $current_time->add(new DateInterval('P1D'))->toDateTimeString(); //add a day and convert to string
            $current_timestamp = Carbon::now('Europe/Lisbon')->toDateTimeString(); //convert to string
                        
            if($event_date > $current_timestamp && $event_date < $condition_timestamp){
                //get method from another file
                $var = new EventController;
                $var->sendReminderMail($event, [$email->client_email, $email->doctor_email]);
                echo "<pre>", print_r('true');
            }
            else{
                echo "<pre>", print_r('false');
            }



        }
        
        return 0;
    }
}
