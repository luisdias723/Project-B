<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\TruckModel;
use App\Http\Resources\TruckModelResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class TruckModelController extends BaseController
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
        $orderCol = Arr::get($searchParams, 'orderCol', 'truck_models.model');
        $order = Arr::get($searchParams, 'order', 'ASC');


        $TruckModelquery = TruckModel::query()->select('truck_models.id', 'truck_models.model', 'truck_brands.brand','truck_models.brand_id' )
        ->leftJoin("truck_brands","truck_brands.id","truck_models.brand_id" )
        ->orderBy($orderCol, $order);

        if($brand_filter){
            $TruckModelquery ->orWhere('truck_models.brand_id', $brand_filter);
        }
      
        if (!empty($keyword)) {
            $TruckModelquery->where(function($query) use ($keyword){
                    $query->where('truck_brands.brand', 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('truck_models.model', 'LIKE', '%' . $keyword . '%');
            });
        }

        return TruckModelResource::collection( $TruckModelquery->paginate($limit));
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

    public function getTruckModels(Request $request)
    {

        $searchParams = $request->all();

        $brand_id = Arr::get($searchParams, 'brand_id'); 

        $TruckModelquery = TruckModel::query()->select('id', 'model', 'brand_id' );

        if (!empty($brand_id)) {
            $TruckModelquery->where('brand_id',$brand_id);
            
        }
   
        return TruckModelResource::collection($TruckModelquery->get());
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
                    'model' =>'required',
                    'brand_id' => 'required',
                ]
            )
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {  
        $model= TruckModel::create([
            
            'model' => $params['model'],
            'brand_id' => $params['brand_id'],
        ]);
    }
        return new TruckModelResource($model);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TruckModel $truckModel)
    {
        $result = TruckModel::query();
        $result->select('id','model','brand_id')
        ->where("id",$truckModel->id);


       /* $result= TruckFleets::find($truckFleet->id);*/
        return $result->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TruckModel $truckModel)
    {
        $params = $request->all();


        $validator = Validator::make(
            $request->all(),
            array_merge(
                [
                    'model' =>'required',
                    'brand_id' => 'required',
                ]
            )
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {  
            $truckModel->update([
                'model' => $params['model'],
                'brand_id' => $params['brand_id'],
            ]);
     
    
        }
            

              return new TruckModelResource($truckModel);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
