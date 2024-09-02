<?php

namespace App\Http\Controllers\Api;

use App\Models\TruckFleets;
use Illuminate\Http\Request;
use App\Http\Resources\TruckFleetsResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class TruckFleetController extends BaseController
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
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);

        $brand_filter = Arr::get($searchParams, 'brand_id', '');
       // $model_filter = Arr::get($searchParams, 'model', '');
        $type_filter = Arr::get($searchParams, 'type_id', '');
        $date_range = Arr::get($searchParams, 'date_range', '');
    
      
    
        $orderCol = Arr::get($searchParams, 'orderCol', 'truck_fleets.inspection_date');
        $order = Arr::get($searchParams, 'order', 'ASC');
        

        $TruckFleetQuery = TruckFleets::query()
        ->select('truck_fleets.id','truck_fleets.registration','truck_fleets.inspection_date','truck_models.model','transport_types.type','transport_types.id AS type_id','truck_brands.brand' )
        ->leftJoin("truck_models","truck_models.id","truck_fleets.model_id" )
        ->leftJoin("transport_types","transport_types.id","truck_fleets.type_id" )
        ->leftJoin("truck_brands","truck_brands.id","truck_models.brand_id" )
        ->orderBy($orderCol, $order);;

      

        if($brand_filter){
            $TruckFleetQuery->orWhere('truck_brands.id', $brand_filter);
        }

     /*   if($model_filter){
            $TruckFleetQuery->orWhere('truck_models.model', $model_filter);
        }*/

        if($type_filter){
            $TruckFleetQuery->orWhere('transport_types.id', $type_filter);
        }

        if (!empty($keyword)) {
            $TruckFleetQuery->where(function($query) use ($keyword){
                    $query->where('truck_brands.brand', 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('truck_models.model', 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('truck_fleets.registration', 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('transport_types.type', 'LIKE', '%' . $keyword . '%');;
            });

            
        }

        if(!empty($date_range)){
    
            $TruckFleetQuery->where('truck_fleets.inspection_date', '>=', $date_range[0])->where('truck_fleets.inspection_date', '<=', $date_range[1]);
        }


        return TruckFleetsResource::collection($TruckFleetQuery->paginate($limit));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params = $request->all();



        $validator = Validator::make(
            $request->all(),
            array_merge(
                [
                    'registration' => 'required',
                     'model_id' => 'required',
                     'type_id' =>'required',
                    'year'=> 'required',
                    'inspection_date'=> 'required',
                    'max_cargo'=> 'required',
                ]
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {

            $max_cargo_string = (string)$params['max_cargo']; 
        if(strpos($max_cargo_string, ',')){
     
        $max_cargo_string = str_replace(",",".",$max_cargo_string);
    }
        $max_cargo= (float)$max_cargo_string;

       
        $camiao= TruckFleets::create([
            'registration' => $params['registration'],
            'model_id' => $params['model_id'],
            'type_id' => $params['type_id'],
            'year'=> (int)$params['year'],
            'inspection_date'=> $params['inspection_date'],
            'max_cargo'=> $max_cargo,

        ]);
    }

        return new TruckFleetsResource($camiao);
    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TruckFleetModel  $TruckFleetModel
     * @return \Illuminate\Http\Response
     */
    public function show(TruckFleets $truckFleet)

    {
        $result = TruckFleets::query();
        $result->select('truck_fleets.id','truck_fleets.registration', 'truck_fleets.type_id', 'truck_fleets.year','truck_fleets.inspection_date', 'truck_fleets.max_cargo','truck_models.model','transport_types.type','truck_brands.brand','truck_brands.id as brand_id', 'truck_models.id as model_id')
        ->leftJoin("truck_models","truck_models.id","truck_fleets.model_id" )
        ->leftJoin("transport_types","transport_types.id","truck_fleets.type_id" )
        ->leftJoin("truck_brands","truck_brands.id","truck_models.brand_id" )
        ->where("truck_fleets.id",$truckFleet->id);


       /* $result= TruckFleets::find($truckFleet->id);*/
        return $result->first();
    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TruckFleetModel  $TruckFleetModel
     * @return \Illuminate\Http\Response
     */
    public function edit(TruckFleets $TruckFleet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TruckFleetModel  $TruckFleetModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TruckFleets $truckFleet)

    {
        $params = $request->all();


        $validator = Validator::make(
            $request->all(),
            array_merge(
                [
                    'registration' => 'required',
                     'model_id' => 'required',
                     'type_id' =>'required',
                    'year'=> 'required',
                    'inspection_date'=> 'required',
                    'max_cargo'=> 'required',
                ]
            )
        );
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {

        $max_cargo_string = (string)$params['max_cargo']; 
        if(strpos($max_cargo_string, ',')){
     
        $max_cargo_string = str_replace(",",".",$max_cargo_string);
    }
        $max_cargo= (float)$max_cargo_string;


       $truckFleet->update([
            'registration' => $params['registration'],
            'model_id' => $params['model_id'],
            'type_id' => $params['type_id'],
            'year'=> (int)$params['year'],
            'inspection_date'=> $params['inspection_date'],
            'max_cargo'=> $max_cargo,
        ]);    
    }

            return new TruckFleetsResource($truckFleet);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TruckFleetModel  $TruckFleetModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(TruckFleets $truckFleet)
    {
 //dd($truck);
        try {
            $truckFleet->delete();
        } catch (\Exception $ex) {
            response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(null, 204);
    }

    
}
