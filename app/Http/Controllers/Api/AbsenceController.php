<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absences;
use App\Http\Resources\AbsenceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use DB;

class AbsenceController extends Controller
{

    const ITEM_PER_PAGE = 25;

    public function  index(Request $request)
    {
        $searchParams = $request->all();
        $absencesQuery = Absences::query();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $date_filter = Arr::get($searchParams,'date_range');
        $user_id = Arr::get($searchParams,'user_id');
        $query = DB::table('absences')->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days');


        if(empty($date_filter)){
    
            $tabelaFerias = DB::table('absences')->where('type', 1)->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaBaixas = DB::table('absences')->where('type', 2)->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaFaltasJ = DB::table('absences')->where('type', 3)->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaFaltasI = DB::table('absences')->where('type', 4)->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaLicensas = DB::table('absences')->where('type', 5)->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaAcidentes = DB::table('absences')->where('type', 6)->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaConpensacaoF = DB::table('absences')->where('type', 7)->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaLuto = DB::table('absences')->where('type', 8)->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
        } 
        
        if (!empty($date_filter)){
            $tabelaFerias = DB::table('absences')->where([
                ['type', 1],
                ['start_date','>=', $date_filter[0]],
                ['end_date','<=', $date_filter[1]],
            ])->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaBaixas = DB::table('absences')->where([
                ['type', 2],
                ['start_date','>=', $date_filter[0]],
                ['end_date','<=', $date_filter[1]],
            ])->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaFaltasJ = DB::table('absences')->where([
                ['type', 3],
                ['start_date','>=', $date_filter[0]],
                ['end_date','<=', $date_filter[1]],
            ])->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaFaltasI = DB::table('absences')->where([
                ['type', 4],
                ['start_date','>=', $date_filter[0]],
                ['end_date','<=', $date_filter[1]],
            ])->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaLicensas = DB::table('absences')->where([
                ['type', 5],
                ['start_date','>=', $date_filter[0]],
                ['end_date','<=', $date_filter[1]],
            ])->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaAcidentes = DB::table('absences')->where([
                ['type', 6],
                ['start_date','>=', $date_filter[0]],
                ['end_date','<=', $date_filter[1]],
            ])->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaConpensacaoF = DB::table('absences')->where([
                ['type', 7],
                ['start_date','>=', $date_filter[0]],
                ['end_date','<=', $date_filter[1]],
            ])->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
            $tabelaLuto = DB::table('absences')->where([
                ['type', 8],
                ['start_date','>=', $date_filter[0]],
                ['end_date','<=', $date_filter[1]],
            ])->selectRaw('absences.*, DATEDIFF(end_date, start_date) AS days')->get();
        }
         
        return array(
            'ferias'=>$tabelaFerias,
            'baixas'=>$tabelaBaixas,
            'faltasJ'=>$tabelaFaltasJ,
            'faltasI'=>$tabelaFaltasI,
            'licensas'=>$tabelaLicensas,
            'acidentes'=>$tabelaAcidentes,
            'compensacaoF'=>$tabelaConpensacaoF,
            'luto'=>$tabelaLuto, 
        );
    }

    public function getEventsToCalendar(Request $request)
    {
        $searchParams = $request->all();
        $type = Arr::get($searchParams, 'type', '');
        $user = Arr::get($searchParams, 'user_id', '');


        $eventQuery = DB::table('absences')
                    ->leftJoin('users', 'absences.user_id', '=', 'users.id')
                    ->leftJoin('types', 'absences.type', '=', 'types.id')
                    ->where('types.status', '=', true)
                    ->select('users.name as title','absences.id as id','absences.user_id as user_id','types.id as type_id','types.name as type','absences.start_date as start','absences.end_date as end', 'types.color as color',DB::raw('DATEDIFF(absences.end_date, absences.start_date) as days'));
        
        //search by type 
        if(!empty($type)){
            $eventQuery->where('absences.type', $type);        
        }
        
        //search by User
        if(!empty($user)){
            $eventQuery->where('users.id', $user);
        }
        
        return $eventQuery->get();
    }


   public function store(Request $request)
   {
        $params = $request->all();
        $currentUser = Auth::user();
        $user = DB::table('users')->where('users.id','=',$params['user_id'])->value('users.vac_days');
        $vacation = DB::table('vacation_days')
                    ->where('vacation_days.user_id', '=', $params['user_id'])
                    ->where('vacation_days.year', '=', date("Y"))
                    ->value('vacation_days.num_days');
        
        if($params['type'] == 1){
            //Calculate days between start and end
            $start_date = date_create($params['date_range'][0]);
            $end_date = date_create($params['date_range'][1]);
            $days =  date_diff($end_date, $start_date);

            if(($vacation - ($user + $days->d)) >= 0){
                $absence = Absences::create([
                    'user_id' => $params['user_id'],
                    'type' => $params['type'],
                    'start_date' => $params['date_range'][0],
                    'end_date' => $params['date_range'][1],
                    'created_by'=> $currentUser->id,
                    'confirmed'=> (int)$params['confirmed'], 
                ]);

                $userUpdate = DB::table('users')->where('users.id', '=', $params['user_id'])->update(['vac_days' => $user + $days->d]);

            } else {
                return response()->json(['error' => 'Não tem dias Disponiveis'], 404);
            }
        } else {
            $absence = Absences::create([
                'user_id' => $params['user_id'],
                'type' => $params['type'],
                'start_date' => $params['date_range'][0],
                'end_date' => $params['date_range'][1],
                'created_by'=> $currentUser->id,
                'confirmed'=> (int)$params['confirmed'], 
            ]);
        }
               
        return new AbsenceResource($absence);
   }

    public function updateFerias(Request $request)
    {
        $params = $request->all();
        $currentUser = Auth::user();

        $user = DB::table('users')->where('users.id','=',$params['user_id'])->value('users.vac_days');
        $vacation = DB::table('vacation_days')
                    ->where('vacation_days.user_id', '=', $params['user_id'])
                    ->where('vacation_days.year', '=', date("Y"))
                    ->value('vacation_days.num_days');
        
        $absence = Absences::find($params['id']);

        $old_start_date = date_create($absence['start_date']);
        $old_end_date = date_create($absence['end_date']);
        $old_days = date_diff($old_end_date, $old_start_date);
        $o_days = $old_days->d;
        
        $start_date = date_create($params['date_range'][0]);
        $end_date = date_create($params['date_range'][1]);
        $calcDays = date_diff($end_date, $start_date);
        $days = $calcDays->d;
        
        $user-
        $dias = $user - $o_days;
        $disD = $vacation - $user;
        if($disD < 0 ){
            $disD = 0;
        }
       
        if($absence['type'] == $params['type']){
            if($vacation - ($dias + $days)>=0){
                $this->removeVacDays($params['user_id'], $dias);
                
                $absence->update([
                    'user_id' => $params['user_id'],
                    'type' => $params['type'],
                    'start_date' => $params['date_range'][0],
                    'end_date' => $params['date_range'][1],
                    'created_by' => $currentUser->id,
                    'confirmed' => (int)$params['confirmed'],
                ]);
                
                $userUpdate = DB::table('users')->where('users.id', '=', $params['user_id'])->update(['vac_days' => $dias + $days]);

            } else {
                return response()->json(['error' => 'Somente '.$disD.' Disponiveis'], 404);
            }
        } else if($absence['type'] != $params['type']){
            if($vacation - ($dias + $days) >=0 ){

                $absence->update([
                    'user_id' => $params['user_id'],
                    'type' => $params['type'],
                    'start_date' => $params['date_range'][0],
                    'end_date' => $params['date_range'][1],
                    'created_by' => $currentUser->id,
                    'confirmed' => (int)$params['confirmed'],
                ]);

                $userUpdate = DB::table('users')->where('users.id', '=', $params['user_id'])->update(['vac_days' => $user + $days]);
            } else {
                return response()->json(['error' => 'Somente '.$disD.' Disponiveis'], 404);
            }
        }
        return new AbsenceResource($request);
    }

    private function removeVacDays($user_id, $days){
        $user = DB::table('users')->where('users.id', '=', $user_id)->update(['users.vac_days'=> $days]);
    }
   
   public function update(Request $request, Absences $absence)
   {
        $params = $request->all();
        $currentUser = Auth::user();
        
        $user = DB::table('users')->where('users.id','=',$params['user_id'])->value('users.vac_days');
        $vacation = DB::table('vacation_days')
                    ->where('vacation_days.user_id', '=', $params['user_id'])
                    ->where('vacation_days.year', '=', date("Y"))
                    ->value('vacation_days.num_days');

        if($absence['type'] != $request['type'] && $absence['type'] == 1){

            $old_start_date = date_create($absence['start_date']);
            $old_end_date = date_create($absence['end_date']);
            $old_days = date_diff($old_end_date, $old_start_date);
            $o_days = $old_days->d;
            
            $this->removeVacDays($absence['user_id'], $o_days);

            $absence->update([
                'user_id'=> $params['user_id'],
                'type'=> $params['type'],
                'start_date'=> $params['date_range'][0],
                'end_date'=> $params['date_range'][1],
                'created_by'=> $currentUser->id,
                'confirmed'=> (int)$params['confirmed'],
            ]);

            $userUpdate = DB::table('users')->where('users.id', '=', $params['user_id'])->update(['vac_days' => $user - $o_days]);
        } else{
            $absence->update([
                'user_id'=> $params['user_id'],
                'type'=> $params['type'],
                'start_date'=> $params['date_range'][0],
                'end_date'=> $params['date_range'][1],
                'created_by'=> $currentUser->id,
                'confirmed'=> (int)$params['confirmed'],
            ]);
        }
        return new AbsenceResource($absence);
   }
   
   public function destroy(Absences $absence)
    {   
        if($absence['type']==1){
            $user = DB::table('users')->where('users.id','=',$absence['user_id'])->value('users.vac_days');

            $old_start_date = date_create($absence['start_date']);
            $old_end_date = date_create($absence['end_date']);
            $old_days = date_diff($old_end_date, $old_start_date);
            $o_days = $old_days->d;
            
            $this->removeVacDays($absence['user_id'], $o_days);
            
            $userUpdate = DB::table('users')->where('users.id', '=', $absence['user_id'])->update(['vac_days' => $user - $o_days]);
        
            try{
                $absence->delete();
            } catch (\Exception $ex) {
                response()->json(['error' => $ex->getMessage()], 403);
            }

        }else{
            try {
                $absence->delete();
            } catch (\Exception $ex) {
                response()->json(['error' => $ex->getMessage()], 403);
            }
        }

       return response()->json(null, 204);
    }

}
