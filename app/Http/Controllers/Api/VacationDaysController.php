<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VacationDaysResource;
use App\Models\VacationDays;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use DB;

class VacationDaysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function getVacations(Request $request)
   {
        $searchparams = $request->all();
        $user = Arr::get($searchparams, 'user_id', 'vac_days', '');
        
        $vacations = DB::table('vacation_days')
                    ->leftJoin('users', 'vacation_days.user_id', '=', 'users.id')
                    ->select('vacation_days.num_days as num_days','vacation_days.year as year')
                    ->where('vacation_days.user_id', '=', $user)
                    ->get();

        return new VacationDaysResource($vacations);
   }

    

}
