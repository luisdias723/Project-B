<?php

namespace App\Http\Controllers\Api;

use App\Models\Garage_Status;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Garage;
use App\Http\Resources\GarageResource;
use Illuminate\Support\Arr;
use DB;

class GarageController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    const ITEM_PER_PAGE = 25;

    public function index(Request $request)
    {
        $searchParams = $request->all();
        $keyword = Arr::get($searchParams, 'keyword', '');

        $garage_filter = Arr::get($searchParams, 'garage_status_id', '');
        //$status_filter = Arr::get($searchParams, 'registration', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $order = Arr::get($searchParams, 'order', 'ASC');


        $TruckGarageQuery = Garage::query();
        $TruckGarageQuery->select('garage.id as id', 'garage.registration_id as matricula_id',  'truck_fleets.registration as matricula', 'garage.date_report as dataReporte', 'garage.reason as reason','garage.date_start as dataInicio', 'garage.date_end as dataConclusao', 'garage.status as booleanStatus','garage.breakdowns_id as tipoAvaria_id','breakdowns.type as tipoAvaria', 'garage.garage_status_id as status_id', 'garage_status.description as status');
        $TruckGarageQuery->leftJoin('truck_fleets', 'truck_fleets.id', '=', 'garage.registration_id');
        $TruckGarageQuery->leftJoin('breakdowns', 'breakdowns.id', '=', 'garage.breakdowns_id');
        $TruckGarageQuery->leftJoin('garage_status', 'garage_status.id', '=', 'garage.garage_status_id');
        $TruckGarageQuery->orderBy('garage.date_report', $order);


        if ($garage_filter) {
            $TruckGarageQuery->orWhere('garage_status.id', $garage_filter);
        }

        // if ($garage_filter) {
        //     $TruckGarageQuery->orWhere('truck_fleets.id', $garage_filter);
        // }

        if (!empty($keyword)) {
            $TruckGarageQuery->where(function ($query) use ($keyword) {
                $query->where('garage_status.description', 'LIKE', '%' . $keyword . '%');
                //$query->orWhere('garage_status.description', 'LIKE', '%' . $keyword . '%');

            });
        }


        return GarageResource::collection($TruckGarageQuery->paginate($limit));

    }

    public function getTruckFleets()
    {

        $registrations = DB::table('truck_fleets')
        ->select('truck_fleets.id as matricula_id', 'truck_fleets.registration as matricula')
        ->get();

        return $registrations;

    }
    public function getTruckStatus()
    {

        $statusDescription = DB::table('garage_status')
        ->select('garage_status.id as status_id', 'garage_status.description as status')
        ->get();

        return $statusDescription;

    }

    public function getTruckBreakdows(){

        $breakdowsDescription = DB::table('breakdowns')
        ->select('breakdowns.id as tipoAvaria_id', 'breakdowns.type as tipoAvaria')
        ->get();

        return $breakdowsDescription;
    }



     public function store(Request $request)
     {
         $params = $request->all();

         $validator = Validator::make(
             $request->all(),
             array_merge(
                 [
                    'matricula_id' => 'required',
                    'dataReporte' => 'required',
                    'dataInicio' => 'required',
                    'dataConclusao' => 'required',
                    'tipoAvaria_id' => 'required',
                    'status_id' => 'required',
                    'reason' => 'required',
                ]
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {

            $garage = Garage::create([

                'registration_id' => $params['matricula_id'],
                'date_report' => $params['dataReporte'],
                'date_start' => $params['dataInicio'],
                'date_end' => $params['dataConclusao'],
                'breakdowns_id' => $params['tipoAvaria_id'],
                'garage_status_id' => $params['status_id'],
                'reason' => $params['reason'],
            ]);
        }



        return new GarageResource($garage);
   }

    public function show(Garage $garage)
    {
        // $result = Garage::query();
        // $result->select('garage.registration_id', 'garage.date_report', 'garage.date_start', 'garage.date_end', 'garage.registration_id', 'garage.breakdowns_id', 'garage.garage_status_id')
        //     ->leftJoin('truck_fleets', 'truck_fleets.id', '=', 'garage.registration_id')
        //     ->leftJoin('breakdowns', 'breakdowns.id', '=', 'garage.breakdowns_id')
        //     ->where('garage.id', $garage->id);

        // return $result->first();
      
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, Garage $garage)
    {

        $params = $request->all();

        $validator = Validator::make(
            $request->all(),
            array_merge(
                [
                    'matricula_id' => 'required',
                    'dataReporte' => 'required',
                    'dataInicio' => 'required',
                    'dataConclusao' => 'required',
                    'tipoAvaria_id' => 'required',
                    'status_id' => 'required',
                    'reason' => 'required',
                ]
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $garage->update([

                'registration_id' => $params['matricula_id'],
                'date_report' => $params['dataReporte'],
                'date_start' => $params['dataInicio'],
                'date_end' => $params['dataConclusao'],
                'breakdowns_id' => $params['tipoAvaria_id'],
                'garage_status_id' => $params['status_id'],
                'reason' => $params['reason'],
            ]);

        }
        return new GarageResource($garage);
    }

    public function updateStatus(Request $request)  
    {
        $params = $request->all(); 
        $statusQuery = DB::table('garage')->where('garage.id', '=', $params['id'])->update(['garage.status' => $params['booleanStatus']]);

        return $statusQuery;
    }

    public function destroy()
    {
    }

}