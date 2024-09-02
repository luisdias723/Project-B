<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\TransportType;
use App\Http\Resources\TransportTypeResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class TransportTypeController extends BaseController
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
        $order = Arr::get($searchParams, 'order', 'ASC');

        

        $TransportTypequery = TransportType::query()->select('id', 'type' )
                               ->orderBy('type', $order);
   
      
        if (!empty($keyword)) {
            $TransportTypequery->where(function($query) use ($keyword){
                    $query->where('type', 'LIKE', '%' . $keyword . '%');
            });
        }

        return TransportTypeResource::collection( $TransportTypequery->paginate($limit));
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
                    'type' => 'required|min:4',
                ]
            )
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {  
            $type= TransportType::create([
                'type' => $params['type'],
            ]);
         
        }     
         
           
    
            return new TransportTypeResource($type);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TransportType $transportType)
    {
        $result = TransportType::query();
        $result->select('id','type')
        ->where("id",$transportType->id);


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
    public function update(Request $request, TransportType $transportType)
    {
        $params = $request->all();

        $validator = Validator::make(
            $request->all(),
            array_merge(
                [
                    'type' => 'required|min:4',
                ]
            )
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {  
            $transportType->update([
                'type' => $params['type'],
                
            ]);
         
        }     
      
       

              return new TransportTypeResource($transportType);
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

    public function getTransportTypes(Request $request)
    {

        $searchParams = $request->all();

        $TransportTypequery = TransportType::query()->select('id', 'type');

        return TransportTypeResource::collection($TransportTypequery ->get());
    }
}
